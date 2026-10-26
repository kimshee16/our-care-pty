<?php

namespace App\Services;

use App\Models\DocumentSigningRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class DocuSignService
{
    public function configurationErrors(): array
    {
        $required = [
            'integration_key' => 'DOCUSIGN_INTEGRATION_KEY',
            'user_id' => 'DOCUSIGN_USER_ID',
            'private_key' => 'DOCUSIGN_PRIVATE_KEY',
            'account_id' => 'DOCUSIGN_ACCOUNT_ID',
            'base_path' => 'DOCUSIGN_BASE_PATH',
        ];

        $missing = [];

        foreach ($required as $key => $label) {
            if (! config("services.docusign.{$key}")) {
                $missing[] = $label;
            }
        }

        return $missing;
    }

    public function isConfigured(): bool
    {
        return $this->configurationErrors() === [];
    }

    public function consentUrl(): ?string
    {
        if (! config('services.docusign.integration_key')) {
            return null;
        }

        return 'https://' . $this->oauthAudience() . '/oauth/auth?' . http_build_query([
            'response_type' => 'code',
            'scope' => 'signature impersonation',
            'client_id' => config('services.docusign.integration_key'),
            'redirect_uri' => config('services.docusign.consent_redirect_uri') ?: config('app.url'),
        ], '', '&', PHP_QUERY_RFC3986);
    }

    public function isConsentRequired(Throwable $exception): bool
    {
        do {
            if ($exception instanceof RequestException
                && $exception->response->status() === 400
                && $exception->response->json('error') === 'consent_required') {
                return true;
            }

            $exception = $exception->getPrevious();
        } while ($exception);

        return false;
    }

    public function sendEnvelope(DocumentSigningRequest $signingRequest): DocumentSigningRequest
    {
        $this->ensureConfigured();
        $signingRequest->loadMissing('client');

        $payload = $this->buildEnvelopePayload($signingRequest);
        $response = $this->api()
            ->post($this->accountPath('/envelopes'), $payload)
            ->throw()
            ->json();

        $signingRequest->forceFill([
            'docusign_envelope_id' => $response['envelopeId'] ?? null,
            'docusign_client_user_id' => $this->clientUserId($signingRequest),
            'status' => DocumentSigningRequest::STATUS_SENT,
            'provider_status' => $response['status'] ?? 'sent',
            'sent_at' => now(),
            'provider_payload' => $response,
        ])->save();

        return $signingRequest;
    }

    public function createRecipientView(DocumentSigningRequest $signingRequest): string
    {
        $this->ensureConfigured();
        $signingRequest->loadMissing('client');

        if (! $signingRequest->docusign_envelope_id) {
            throw new RuntimeException('This signing request has not been sent to DocuSign yet.');
        }

        $response = $this->api()
            ->post($this->accountPath("/envelopes/{$signingRequest->docusign_envelope_id}/views/recipient"), [
                'returnUrl' => route('document-signing.return', $signingRequest),
                'authenticationMethod' => 'none',
                'email' => $signingRequest->client->email,
                'userName' => $this->recipientName($signingRequest),
                'recipientId' => $signingRequest->docusign_recipient_id ?: '1',
                'clientUserId' => $this->clientUserId($signingRequest),
            ])
            ->throw()
            ->json();

        $url = $response['url'] ?? null;

        if (! $url) {
            throw new RuntimeException('DocuSign did not return a signing URL.');
        }

        $signingRequest->forceFill([
            'signing_url' => $url,
            'signing_url_expires_at' => now()->addMinutes(5),
            'status' => DocumentSigningRequest::STATUS_SIGNING_READY,
            'last_synced_at' => now(),
        ])->save();

        return $url;
    }

    public function syncEnvelope(DocumentSigningRequest $signingRequest): DocumentSigningRequest
    {
        $this->ensureConfigured();

        if (! $signingRequest->docusign_envelope_id) {
            throw new RuntimeException('This signing request has no DocuSign envelope ID.');
        }

        $envelope = $this->api()
            ->get($this->accountPath("/envelopes/{$signingRequest->docusign_envelope_id}"))
            ->throw()
            ->json();

        return $this->applyProviderStatus($signingRequest, $envelope);
    }

    public function downloadSignedDocument(DocumentSigningRequest $signingRequest): string
    {
        $this->ensureConfigured();

        if (! $signingRequest->isComplete()) {
            throw new RuntimeException('This signing request is not completed yet.');
        }

        if (! $signingRequest->docusign_envelope_id) {
            throw new RuntimeException('This signing request has no DocuSign envelope ID.');
        }

        $document = $this->api()
            ->accept('application/pdf')
            ->get($this->accountPath("/envelopes/{$signingRequest->docusign_envelope_id}/documents/combined"), [
                'certificate' => 'true',
            ])
            ->throw()
            ->body();

        if (! str_starts_with($document, '%PDF-')) {
            throw new RuntimeException('DocuSign did not return a PDF document.');
        }

        return $document;
    }

    public function voidEnvelope(DocumentSigningRequest $signingRequest, string $reason): DocumentSigningRequest
    {
        $this->ensureConfigured();

        if (! $signingRequest->docusign_envelope_id) {
            $signingRequest->forceFill([
                'status' => DocumentSigningRequest::STATUS_VOIDED,
                'voided_at' => now(),
            ])->save();

            return $signingRequest;
        }

        $response = $this->api()
            ->put($this->accountPath("/envelopes/{$signingRequest->docusign_envelope_id}"), [
                'status' => 'voided',
                'voidedReason' => $reason,
            ])
            ->throw()
            ->json();

        $signingRequest->forceFill([
            'status' => DocumentSigningRequest::STATUS_VOIDED,
            'provider_status' => 'voided',
            'voided_at' => now(),
            'last_synced_at' => now(),
            'provider_payload' => $response ?: $signingRequest->provider_payload,
        ])->save();

        return $signingRequest;
    }

    public function applyProviderStatus(DocumentSigningRequest $signingRequest, array $payload): DocumentSigningRequest
    {
        $providerStatus = strtolower((string) ($payload['status'] ?? $payload['envelopeStatus'] ?? ''));
        $status = match ($providerStatus) {
            'completed' => DocumentSigningRequest::STATUS_COMPLETED,
            'voided' => DocumentSigningRequest::STATUS_VOIDED,
            'declined' => DocumentSigningRequest::STATUS_DECLINED,
            default => $signingRequest->status,
        };

        $updates = [
            'provider_status' => $providerStatus ?: $signingRequest->provider_status,
            'status' => $status,
            'last_synced_at' => now(),
            'provider_payload' => $payload,
        ];

        if ($status === DocumentSigningRequest::STATUS_COMPLETED && ! $signingRequest->signed_at) {
            $updates['signed_at'] = now();
        }

        if ($status === DocumentSigningRequest::STATUS_VOIDED && ! $signingRequest->voided_at) {
            $updates['voided_at'] = now();
        }

        $signingRequest->forceFill($updates)->save();

        return $signingRequest;
    }

    protected function buildEnvelopePayload(DocumentSigningRequest $signingRequest): array
    {
        $base = [
            'emailSubject' => $signingRequest->title,
            'emailBlurb' => $signingRequest->message,
            'status' => 'sent',
            'notification' => [
                'useAccountDefaults' => false,
                'expirations' => [
                    'expireEnabled' => true,
                    'expireAfter' => '10',
                    'expireWarn' => '1',
                ],
            ],
        ];

        if ($signingRequest->docusign_template_id) {
            $templateRole = [
                'roleName' => config('services.docusign.template_role', 'Client'),
                'name' => $this->recipientName($signingRequest),
                'email' => $signingRequest->client->email,
                'recipientId' => $signingRequest->docusign_recipient_id ?: '1',
                'clientUserId' => $this->clientUserId($signingRequest),
            ];

            if ($this->shouldSendEmailToEmbeddedSigner()) {
                $templateRole['embeddedRecipientStartURL'] = 'SIGN_AT_DOCUSIGN';
            }

            return $base + [
                'templateId' => $signingRequest->docusign_template_id,
                'templateRoles' => [$templateRole],
            ];
        }

        $documentFiles = $signingRequest->documentFiles();

        if ($documentFiles === []) {
            throw new RuntimeException('A stored document or DocuSign template ID is required before sending.');
        }

        $documents = [];

        foreach ($documentFiles as $index => $documentFile) {
            if (! Storage::disk('local')->exists($documentFile['path'])) {
                throw new RuntimeException('A stored document could not be found: ' . ($documentFile['original_name'] ?? basename($documentFile['path'])));
            }

            $fileName = $documentFile['original_name'] ?? basename($documentFile['path']);
            $documents[] = [
                'documentBase64' => base64_encode(Storage::disk('local')->get($documentFile['path'])),
                'name' => $fileName,
                'fileExtension' => pathinfo($fileName, PATHINFO_EXTENSION) ?: 'pdf',
                'documentId' => (string) ($index + 1),
            ];
        }

        $signHereTabs = collect($documentFiles)
            ->map(fn ($documentFile, $index) => [
                'documentId' => (string) ($index + 1),
                'pageNumber' => (string) ($documentFile['signature_page'] ?? 1),
                'xPosition' => (string) ($documentFile['signature_x'] ?? config('services.docusign.sign_x_position', 420)),
                'yPosition' => (string) ($documentFile['signature_y'] ?? config('services.docusign.sign_y_position', 650)),
            ])
            ->all();

        $signer = [
            'email' => $signingRequest->client->email,
            'name' => $this->recipientName($signingRequest),
            'recipientId' => $signingRequest->docusign_recipient_id ?: '1',
            'routingOrder' => '1',
            'clientUserId' => $this->clientUserId($signingRequest),
            'tabs' => [
                'signHereTabs' => $signHereTabs,
            ],
        ];

        if ($this->shouldSendEmailToEmbeddedSigner()) {
            $signer['embeddedRecipientStartURL'] = 'SIGN_AT_DOCUSIGN';
        }

        return $base + [
            'documents' => $documents,
            'recipients' => [
                'signers' => [$signer],
            ],
        ];
    }

    protected function api()
    {
        return Http::withToken($this->accessToken())
            ->baseUrl(rtrim(config('services.docusign.base_path'), '/') . '/v2.1')
            ->acceptJson()
            ->asJson();
    }

    protected function accessToken(): string
    {
        return Cache::remember('docusign.jwt_access_token', now()->addMinutes(50), function () {
            try {
                $response = Http::asForm()
                    ->post($this->oauthUrl('/oauth/token'), [
                        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                        'assertion' => $this->jwtAssertion(),
                    ])
                    ->throw()
                    ->json();
            } catch (RequestException $exception) {
                if ($this->isConsentRequired($exception)) {
                    throw new RuntimeException(
                        'DocuSign JWT consent is required before envelopes can be sent. Open the DocuSign consent link as the configured impersonated user, accept the signature and impersonation scopes, then retry sending.',
                        0,
                        $exception
                    );
                }

                throw $exception;
            }

            return $response['access_token'] ?? throw new RuntimeException('DocuSign did not return an access token.');
        });
    }

    protected function jwtAssertion(): string
    {
        $now = time();
        $header = ['alg' => 'RS256', 'typ' => 'JWT'];
        $payload = [
            'iss' => config('services.docusign.integration_key'),
            'sub' => config('services.docusign.user_id'),
            'aud' => $this->oauthAudience(),
            'iat' => $now,
            'exp' => $now + 3600,
            'scope' => 'signature impersonation',
        ];

        $unsignedToken = $this->base64UrlEncode(json_encode($header)) . '.' . $this->base64UrlEncode(json_encode($payload));
        $privateKey = str_replace('\n', "\n", (string) config('services.docusign.private_key'));

        if (! openssl_sign($unsignedToken, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
            throw new RuntimeException('Unable to sign the DocuSign JWT assertion. Check DOCUSIGN_PRIVATE_KEY.');
        }

        return $unsignedToken . '.' . $this->base64UrlEncode($signature);
    }

    protected function accountPath(string $path): string
    {
        return '/accounts/' . config('services.docusign.account_id') . $path;
    }

    protected function oauthUrl(string $path): string
    {
        return 'https://' . $this->oauthAudience() . $path;
    }

    protected function oauthAudience(): string
    {
        return config('services.docusign.oauth_base_path')
            ?: (config('services.docusign.demo') ? 'account-d.docusign.com' : 'account.docusign.com');
    }

    protected function recipientName(DocumentSigningRequest $signingRequest): string
    {
        $client = $signingRequest->client;
        $name = trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? ''));

        return $name !== '' ? $name : $client->email;
    }

    protected function clientUserId(DocumentSigningRequest $signingRequest): string
    {
        return (string) ($signingRequest->docusign_client_user_id ?: 'client-' . $signingRequest->client_id);
    }

    protected function shouldSendEmailToEmbeddedSigner(): bool
    {
        return (bool) config('services.docusign.send_email_to_embedded_signers', true);
    }

    protected function ensureConfigured(): void
    {
        $missing = $this->configurationErrors();

        if ($missing !== []) {
            throw new RuntimeException('DocuSign is not configured. Missing: ' . implode(', ', $missing));
        }
    }

    protected function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}

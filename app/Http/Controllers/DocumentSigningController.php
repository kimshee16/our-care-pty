<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\DocumentSigningRequest;
use App\Models\User;
use App\Services\DocuSignService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class DocumentSigningController extends Controller
{
    public function __construct(protected DocuSignService $docuSign)
    {
    }

    public function index()
    {
        $user = $this->getUserModel();

        if (! $user || ! in_array($user->accounttype, ['admin', 'client'], true)) {
            return redirect('/login');
        }

        $documents = DocumentSigningRequest::with(['client', 'creator'])
            ->when($user->accounttype === 'client', fn ($query) => $query->where('client_id', $user->record_id))
            ->latest()
            ->get();

        $syncFailed = false;

        if ($this->docuSign->isConfigured()) {
            foreach ($documents as $document) {
                if (! $document->docusign_envelope_id) {
                    continue;
                }

                try {
                    $this->docuSign->syncEnvelope($document);
                } catch (Throwable $exception) {
                    $syncFailed = true;
                    Log::warning('DocuSign automatic status sync failed.', [
                        'document_signing_request_id' => $document->id,
                        'error' => $exception->getMessage(),
                    ]);
                }
            }
        }

        $clients = $user->accounttype === 'admin'
            ? Client::orderBy('first_name')->orderBy('last_name')->get()
            : collect();

        return view('document-signing.index', [
            'documents' => $documents,
            'clients' => $clients,
            'isAdmin' => $user->accounttype === 'admin',
            'syncFailed' => $syncFailed,
            'configMissing' => $this->docuSign->configurationErrors(),
            'docusignConsentUrl' => session('docusign_consent_url'),
        ]);
    }

    public function store(Request $request)
    {
        $user = $this->requireAdmin();

        $validated = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'title' => ['required', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:2000'],
            'documents' => ['required', 'array', 'min:1', 'max:10'],
            'documents.*' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:15360'],
            'signature_positions' => ['nullable', 'array'],
            'signature_positions.*.page' => ['nullable', 'integer', 'min:1'],
            'signature_positions.*.x' => ['nullable', 'integer', 'min:0'],
            'signature_positions.*.y' => ['nullable', 'integer', 'min:0'],
        ], [
            'documents.required' => 'Please upload at least one document that the client needs to sign.',
            'documents.*.mimes' => 'Each document must be a PDF, DOC, or DOCX file.',
            'documents.*.max' => 'Each document must be 15 MB or smaller.',
        ]);

        $documentFiles = [];
        $signaturePositions = $validated['signature_positions'] ?? [];

        foreach ($request->file('documents', []) as $index => $file) {
            $position = $signaturePositions[$index] ?? [];

            $documentFiles[] = [
                'path' => $file->store('document-signing/' . $validated['client_id']),
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'signature_page' => (int) ($position['page'] ?? 1),
                'signature_x' => (int) ($position['x'] ?? config('services.docusign.sign_x_position', 420)),
                'signature_y' => (int) ($position['y'] ?? config('services.docusign.sign_y_position', 650)),
            ];
        }

        $firstDocument = $documentFiles[0] ?? null;

        DocumentSigningRequest::create([
            'client_id' => $validated['client_id'],
            'created_by' => $user->id,
            'title' => $validated['title'],
            'message' => $validated['message'] ?? null,
            'document_path' => $firstDocument['path'] ?? null,
            'document_original_name' => $firstDocument['original_name'] ?? null,
            'document_files' => $documentFiles,
            'status' => DocumentSigningRequest::STATUS_DRAFT,
        ]);

        return redirect('/admin/document-signing')->with('success', 'Document signing request created.');
    }

    public function send(int $id)
    {
        $this->requireAdmin();
        $signingRequest = DocumentSigningRequest::with('client')->findOrFail($id);

        if (! $signingRequest->canBeSent()) {
            return redirect('/admin/document-signing')->with('error', 'Only draft or failed documents can be sent.');
        }

        try {
            $this->docuSign->sendEnvelope($signingRequest);
        } catch (Throwable $exception) {
            Log::error('DocuSign envelope send failed.', [
                'document_signing_request_id' => $signingRequest->id,
                'error' => $exception->getMessage(),
            ]);

            $signingRequest->forceFill(['status' => DocumentSigningRequest::STATUS_FAILED])->save();

            $redirect = redirect('/admin/document-signing')->with('error', $exception->getMessage());

            if ($this->docuSign->isConsentRequired($exception) && $this->docuSign->consentUrl()) {
                $redirect->with('docusign_consent_url', $this->docuSign->consentUrl());
            }

            return $redirect;
        }

        return redirect('/admin/document-signing')->with('success', 'Document sent to DocuSign for client signature.');
    }

    public function sign(int $id)
    {
        $user = $this->requireClient();
        $signingRequest = $this->clientOwnedRequest($id, $user);

        if ($signingRequest->isComplete()) {
            return redirect('/client/document-signing')->with('success', 'This document is already signed.');
        }

        try {
            $url = $this->docuSign->createRecipientView($signingRequest);
        } catch (Throwable $exception) {
            Log::error('DocuSign signing URL creation failed.', [
                'document_signing_request_id' => $signingRequest->id,
                'error' => $exception->getMessage(),
            ]);

            return redirect('/client/document-signing')->with('error', $exception->getMessage());
        }

        return redirect()->away($url);
    }

    public function sync(int $id)
    {
        $user = $this->getUserModel();

        if (! $user || ! in_array($user->accounttype, ['admin', 'client'], true)) {
            return redirect('/login');
        }

        $signingRequest = $user->accounttype === 'admin'
            ? DocumentSigningRequest::findOrFail($id)
            : $this->clientOwnedRequest($id, $user);

        try {
            $this->docuSign->syncEnvelope($signingRequest);
        } catch (Throwable $exception) {
            Log::error('DocuSign envelope sync failed.', [
                'document_signing_request_id' => $signingRequest->id,
                'error' => $exception->getMessage(),
            ]);

            return redirect($this->documentSigningPath($user))->with('error', $exception->getMessage());
        }

        return redirect($this->documentSigningPath($user))->with('success', 'DocuSign status synced.');
    }

    public function void(Request $request, int $id)
    {
        $this->requireAdmin();
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $signingRequest = DocumentSigningRequest::findOrFail($id);

        try {
            $this->docuSign->voidEnvelope($signingRequest, $validated['reason'] ?? 'Voided from Our Care admin.');
        } catch (Throwable $exception) {
            Log::error('DocuSign envelope void failed.', [
                'document_signing_request_id' => $signingRequest->id,
                'error' => $exception->getMessage(),
            ]);

            return redirect('/admin/document-signing')->with('error', $exception->getMessage());
        }

        return redirect('/admin/document-signing')->with('success', 'Document signing request voided.');
    }

    public function returnFromSigning(DocumentSigningRequest $documentSigningRequest)
    {
        $user = $this->getUserModel();

        if ($documentSigningRequest->docusign_envelope_id && $this->docuSign->isConfigured()) {
            try {
                $this->docuSign->syncEnvelope($documentSigningRequest);
            } catch (Throwable $exception) {
                Log::warning('DocuSign return sync failed.', [
                    'document_signing_request_id' => $documentSigningRequest->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        return redirect($user ? $this->documentSigningPath($user) : '/login')
            ->with('success', 'Returned from DocuSign. The document status has been refreshed where possible.');
    }

    public function webhook(Request $request)
    {
        $payload = $request->all();
        $raw = $request->getContent();

        if ($payload === [] && trim($raw) !== '' && str_starts_with(ltrim($raw), '<')) {
            $payload = $this->xmlWebhookPayload($raw);
        }

        $envelopeId = $this->extractEnvelopeId($request, $payload);

        if (! $envelopeId) {
            return response()->json(['ok' => false, 'message' => 'Envelope ID not found.'], 422);
        }

        $signingRequest = DocumentSigningRequest::where('docusign_envelope_id', $envelopeId)->first();

        if (! $signingRequest) {
            return response()->json(['ok' => true, 'message' => 'Envelope ignored.']);
        }

        $this->docuSign->applyProviderStatus($signingRequest, $payload);

        return response()->json(['ok' => true]);
    }

    public function download(int $id, ?int $fileIndex = null)
    {
        $user = $this->getUserModel();

        if (! $user || ! in_array($user->accounttype, ['admin', 'client'], true)) {
            return redirect('/login');
        }

        $signingRequest = $user->accounttype === 'admin'
            ? DocumentSigningRequest::findOrFail($id)
            : $this->clientOwnedRequest($id, $user);

        if ($signingRequest->isComplete()) {
            try {
                $document = $this->docuSign->downloadSignedDocument($signingRequest);
            } catch (Throwable $exception) {
                Log::warning('DocuSign signed document download failed.', [
                    'document_signing_request_id' => $signingRequest->id,
                    'error' => $exception->getMessage(),
                ]);

                return redirect($this->documentSigningPath($user))
                    ->with('error', 'Unable to download the signed document from DocuSign. Please try again later.');
            }

            return response()->streamDownload(function () use ($document) {
                echo $document;
            }, Str::slug($signingRequest->displayReference()) . '-signed.pdf', [
                'Content-Type' => 'application/pdf',
                'Cache-Control' => 'private, no-store',
            ]);
        }

        $files = $signingRequest->documentFiles();
        $file = $files[$fileIndex ?? 0] ?? null;

        if (! $file || ! Storage::disk('local')->exists($file['path'])) {
            abort(404, 'Document not found.');
        }

        return Storage::disk('local')->download($file['path'], $file['original_name'] ?? basename($file['path']));
    }

    protected function extractEnvelopeId(Request $request, array $payload): ?string
    {
        $envelopeId = data_get($payload, 'data.envelopeId')
            ?? data_get($payload, 'envelopeId')
            ?? data_get($payload, 'envelopeStatus.envelopeId');

        if ($envelopeId) {
            return $envelopeId;
        }

        $raw = $request->getContent();

        if (trim($raw) === '' || ! str_starts_with(ltrim($raw), '<')) {
            return null;
        }

        $xml = @simplexml_load_string($raw);

        return $xml ? (string) ($xml->EnvelopeStatus->EnvelopeID ?? $xml->EnvelopeID ?? '') ?: null : null;
    }

    protected function xmlWebhookPayload(string $raw): array
    {
        $xml = @simplexml_load_string($raw);

        if (! $xml) {
            return [];
        }

        return [
            'envelopeId' => (string) ($xml->EnvelopeStatus->EnvelopeID ?? $xml->EnvelopeID ?? ''),
            'status' => (string) ($xml->EnvelopeStatus->Status ?? $xml->Status ?? ''),
        ];
    }

    protected function clientOwnedRequest(int $id, User $user): DocumentSigningRequest
    {
        return DocumentSigningRequest::with('client')
            ->where('id', $id)
            ->where('client_id', $user->record_id)
            ->firstOrFail();
    }

    protected function requireAdmin(): User
    {
        $user = $this->getUserModel();

        if (! $user || $user->accounttype !== 'admin') {
            abort(403);
        }

        return $user;
    }

    protected function requireClient(): User
    {
        $user = $this->getUserModel();

        if (! $user || $user->accounttype !== 'client') {
            abort(403);
        }

        return $user;
    }

    protected function getUserModel(): ?User
    {
        $sessionUser = session('user', []);

        return User::find($sessionUser['id'] ?? null);
    }

    protected function documentSigningPath(User $user): string
    {
        return $user->accounttype === 'admin' ? '/admin/document-signing' : '/client/document-signing';
    }
}

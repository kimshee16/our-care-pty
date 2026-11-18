<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\DocumentSigningRequest;
use App\Models\User;
use App\Exceptions\GoogleDriveException;
use App\Services\DocuSignService;
use App\Services\SigningDocumentImporter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

class DocumentSigningController extends Controller
{
    public function __construct(protected DocuSignService $docuSign)
    {
    }

    public function index()
    {
        $user = $this->signingUser();

        if (! $user) {
            return redirect('/login');
        }

        $isAdmin = $user->accounttype === 'admin';
        $documents = $this->visibleRequests($user)
            ->with(['client', 'healthcareWorkerUser'])
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

        return view('document-signing.index', [
            'documents' => $documents,
            'clients' => $isAdmin ? Client::orderBy('first_name')->orderBy('last_name')->get() : collect(),
            'healthcareWorkers' => $isAdmin
                ? User::where('accounttype', 'healthcare_worker')->orderBy('fullname')->get(['id', 'fullname', 'email'])
                : collect(),
            'isAdmin' => $isAdmin,
            'signingPath' => $this->documentSigningPath($user),
            'syncFailed' => $syncFailed,
            'configMissing' => $this->docuSign->configurationErrors(),
            'docusignConsentUrl' => session('docusign_consent_url'),
            'signingDriveMimeTypes' => SigningDocumentImporter::MIME_TYPES,
        ]);
    }

    public function store(Request $request, SigningDocumentImporter $importer)
    {
        $user = $this->requireAdmin();
        $request->mergeIfMissing(['document_source' => 'manual']);

        if ($request->has('signatory')) {
            $signatory = $request->input('signatory');
            [$type, $id] = array_pad(explode(':', is_string($signatory) ? $signatory : '', 2), 2, null);
            $request->merge([
                'client_id' => $type === 'client' ? $id : null,
                'healthcare_worker_user_id' => $type === 'healthcare_worker' ? $id : null,
            ]);
        }

        $validated = $request->validate([
            'client_id' => ['bail', 'nullable', 'required_without:healthcare_worker_user_id', 'prohibits:healthcare_worker_user_id', 'integer', 'exists:clients,id'],
            'healthcare_worker_user_id' => ['bail', 'nullable', 'integer', Rule::exists('users', 'id')->where('accounttype', 'healthcare_worker')],
            'title' => ['required', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:2000'],
            'document_source' => ['required', 'in:manual,file_manager'],
            'documents' => ['exclude_unless:document_source,manual', 'required', 'array', 'min:1', 'max:10'],
            'documents.*' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:15360'],
            'drive_files' => ['exclude_unless:document_source,file_manager', 'required', 'array', 'min:1', 'max:10'],
            'drive_files.*' => ['required', 'string', 'distinct', 'regex:/^[A-Za-z0-9_-]+$/D'],
            'signature_positions' => ['nullable', 'array'],
            'signature_positions.*.page' => ['nullable', 'integer', 'min:1'],
            'signature_positions.*.x' => ['nullable', 'integer', 'min:0'],
            'signature_positions.*.y' => ['nullable', 'integer', 'min:0'],
        ], [
            'client_id.required_without' => 'Please select a signatory.',
            'client_id.prohibits' => 'Please select only one signatory.',
            'client_id.integer' => 'Please select a valid client.',
            'client_id.exists' => 'Please select a valid client.',
            'healthcare_worker_user_id.integer' => 'Please select a valid healthcare worker.',
            'healthcare_worker_user_id.exists' => 'Please select a valid healthcare worker.',
            'documents.required' => 'Please upload at least one document for the signatory.',
            'documents.*.mimes' => 'Each document must be a PDF, DOC, or DOCX file.',
            'documents.*.max' => 'Each document must be 15 MB or smaller.',
            'drive_files.required' => 'Please choose at least one document from File Manager.',
            'drive_files.max' => 'Choose up to 10 documents.',
        ]);

        $documentFiles = [];
        $signaturePositions = $validated['signature_positions'] ?? [];
        $temporaryFiles = [];
        $clientId = $validated['client_id'] ?? null;
        $healthcareWorkerUserId = $validated['healthcare_worker_user_id'] ?? null;
        $directory = $healthcareWorkerUserId !== null
            ? 'document-signing/healthcare-worker-' . $healthcareWorkerUserId
            : 'document-signing/' . $clientId;

        try {
            $files = $request->file('documents', []);
            if ($validated['document_source'] === 'file_manager') {
                $files = [];
                foreach ($validated['drive_files'] as $id) {
                    $temporaryFiles[] = $file = $importer->download($id);
                    $files[] = $file;
                }
            }

            foreach ($files as $index => $file) {
                $position = $signaturePositions[$index] ?? [];
                $path = $file->store($directory, 'local');
                if (! $path) {
                    throw new \RuntimeException('Unable to store the signing document.');
                }
                $documentFiles[] = [
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'signature_page' => (int) ($position['page'] ?? 1),
                    'signature_x' => (int) ($position['x'] ?? config('services.docusign.sign_x_position', 420)),
                    'signature_y' => (int) ($position['y'] ?? config('services.docusign.sign_y_position', 650)),
                ];
            }

            $firstDocument = $documentFiles[0] ?? null;

            DocumentSigningRequest::create([
                'client_id' => $clientId,
                'healthcare_worker_user_id' => $healthcareWorkerUserId,
                'created_by' => $user->id,
                'title' => $validated['title'],
                'message' => $validated['message'] ?? null,
                'document_path' => $firstDocument['path'] ?? null,
                'document_original_name' => $firstDocument['original_name'] ?? null,
                'document_files' => $documentFiles,
                'status' => DocumentSigningRequest::STATUS_DRAFT,
            ]);
        } catch (Throwable $exception) {
            Storage::disk('local')->delete(array_column($documentFiles, 'path'));
            if ($exception instanceof GoogleDriveException) {
                return back()->withInput($request->except('documents'))
                    ->withErrors(['drive_files' => $exception->getMessage()]);
            }
            throw $exception;
        } finally {
            foreach ($temporaryFiles as $file) {
                if (is_file($file->getPathname())) {
                    unlink($file->getPathname());
                }
            }
        }

        return redirect('/admin/document-signing')->with('success', 'Document signing request created.');
    }

    public function send(int $id)
    {
        $this->requireAdmin();
        $signingRequest = DocumentSigningRequest::findOrFail($id);

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

        return redirect('/admin/document-signing')->with('success', 'Document sent to DocuSign for signature.');
    }

    public function sign(int $id)
    {
        $user = $this->signingUser();
        abort_if(! $user || $user->accounttype === 'admin', 403);

        $signingRequest = $this->visibleRequests($user)->findOrFail($id);
        $signingPath = $this->documentSigningPath($user);

        if ($signingRequest->isComplete()) {
            return redirect($signingPath)->with('success', 'This document is already signed.');
        }

        try {
            $url = $this->docuSign->createRecipientView($signingRequest);
        } catch (Throwable $exception) {
            Log::error('DocuSign signing URL creation failed.', [
                'document_signing_request_id' => $signingRequest->id,
                'error' => $exception->getMessage(),
            ]);

            return redirect($signingPath)->with('error', $exception->getMessage());
        }

        return redirect()->away($url);
    }

    public function sync(int $id)
    {
        $user = $this->signingUser();

        if (! $user) {
            return redirect('/login');
        }

        $signingRequest = $this->visibleRequests($user)->findOrFail($id);

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

    public function returnFromSigning(int $documentSigningRequest)
    {
        $user = $this->signingUser();
        if (! $user) {
            return redirect('/login');
        }

        $signingRequest = $this->visibleRequests($user)->findOrFail($documentSigningRequest);
        if ($signingRequest->docusign_envelope_id && $this->docuSign->isConfigured()) {
            try {
                $this->docuSign->syncEnvelope($signingRequest);
            } catch (Throwable $exception) {
                Log::warning('DocuSign return sync failed.', [
                    'document_signing_request_id' => $signingRequest->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        return redirect($this->documentSigningPath($user))
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
        $user = $this->signingUser();

        if (! $user) {
            return redirect('/login');
        }

        $signingRequest = $this->visibleRequests($user)->findOrFail($id);

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

    protected function visibleRequests(User $user): Builder
    {
        return match ($user->accounttype) {
            'admin' => DocumentSigningRequest::query(),
            'client' => DocumentSigningRequest::whereNotNull('client_id')->where('client_id', $user->record_id),
            'healthcare_worker' => DocumentSigningRequest::where('healthcare_worker_user_id', $user->id),
        };
    }

    protected function requireAdmin(): User
    {
        $user = $this->getUserModel();

        if (! $user || $user->accounttype !== 'admin') {
            abort(403);
        }

        return $user;
    }

    protected function signingUser(): ?User
    {
        $user = $this->getUserModel();

        return $user && in_array($user->accounttype, ['admin', 'client', 'healthcare_worker'], true) ? $user : null;
    }

    protected function getUserModel(): ?User
    {
        $sessionUser = session('user', []);

        return User::find($sessionUser['id'] ?? null);
    }

    protected function documentSigningPath(User $user): string
    {
        return match ($user->accounttype) {
            'admin' => '/admin/document-signing',
            'client' => '/client/document-signing',
            'healthcare_worker' => '/healthcare/document-signing',
        };
    }
}

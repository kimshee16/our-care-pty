<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\DocumentSigningRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class DocumentSigningTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_document_signing_request_for_client(): void
    {
        $admin = User::factory()->create([
            'accounttype' => 'admin',
        ]);

        $client = $this->createClient('kim@example.com');

        $response = $this->withSession([
            'user' => ['id' => $admin->id, 'accounttype' => 'admin'],
        ])->post('/admin/document-signing', [
            'client_id' => $client->id,
            'title' => 'Service Agreement',
            'message' => 'Please review and sign.',
            'documents' => [
                UploadedFile::fake()->create('service-agreement.pdf', 12, 'application/pdf'),
            ],
        ]);

        $response->assertRedirect('/admin/document-signing');

        $this->assertDatabaseHas('document_signing_requests', [
            'client_id' => $client->id,
            'created_by' => $admin->id,
            'title' => 'Service Agreement',
            'status' => DocumentSigningRequest::STATUS_DRAFT,
        ]);

        $signingRequest = DocumentSigningRequest::first();

        $this->assertNotNull($signingRequest->reference_code);
        $this->assertStringStartsWith('DSR-', $signingRequest->reference_code);
        $this->assertCount(1, $signingRequest->documentFiles());
    }

    public function test_client_only_sees_their_own_document_signing_requests(): void
    {
        $this->configureDocuSign();
        Http::preventStrayRequests();
        Http::fake([
            'account-d.docusign.com/oauth/token' => Http::response(['access_token' => 'test-token']),
            'demo.docusign.net/restapi/v2.1/accounts/account-id/envelopes/client-envelope' => Http::response(['status' => 'completed']),
        ]);

        $client = $this->createClient('kim@example.com');
        $otherClient = $this->createClient('other@example.com');

        $clientUser = User::factory()->create([
            'fullname' => 'Kim Ramirez',
            'email' => $client->email,
            'accounttype' => 'client',
            'record_id' => $client->id,
            'approved' => 1,
        ]);

        $visibleRequest = DocumentSigningRequest::create([
            'client_id' => $client->id,
            'reference_code' => 'DSR-20261012-000101',
            'title' => 'Visible Agreement',
            'status' => DocumentSigningRequest::STATUS_SENT,
            'docusign_envelope_id' => 'client-envelope',
        ]);

        $hiddenRequest = DocumentSigningRequest::create([
            'client_id' => $otherClient->id,
            'title' => 'Hidden Agreement',
            'status' => DocumentSigningRequest::STATUS_SENT,
            'docusign_envelope_id' => 'other-envelope',
        ]);

        $response = $this->withSession([
            'user' => ['id' => $clientUser->id, 'accounttype' => 'client', 'approved' => 1],
        ])->get('/client/document-signing');

        $response->assertOk()
            ->assertSee('Visible Agreement')
            ->assertSee('status-completed')
            ->assertDontSee('Hidden Agreement')
            ->assertDontSee('Sync status')
            ->assertDontSee('/client/document-signing/' . $visibleRequest->id . '/sign');

        $this->assertSame(DocumentSigningRequest::STATUS_COMPLETED, $visibleRequest->fresh()->status);
        $this->assertSame(DocumentSigningRequest::STATUS_SENT, $hiddenRequest->fresh()->status);
        $this->assertNull($hiddenRequest->fresh()->last_synced_at);
        Http::assertNotSent(fn ($request) => str_contains($request->url(), 'other-envelope'));
        Http::assertSentCount(2);
    }

    public function test_admin_page_syncs_envelopes_on_every_reload_and_skips_unsent_requests(): void
    {
        $this->configureDocuSign();
        Http::preventStrayRequests();
        Http::fake([
            'account-d.docusign.com/oauth/token' => Http::response(['access_token' => 'test-token']),
            'demo.docusign.net/restapi/v2.1/accounts/account-id/envelopes/reload-envelope' => Http::sequence()
                ->push(['status' => 'sent'])
                ->push(['status' => 'completed'])
                ->push(['status' => 'completed']),
        ]);

        $admin = User::factory()->create(['accounttype' => 'admin']);
        $client = $this->createClient('reload@example.com');
        $sent = DocumentSigningRequest::create([
            'client_id' => $client->id,
            'title' => 'Reload Agreement',
            'status' => DocumentSigningRequest::STATUS_SENT,
            'docusign_envelope_id' => 'reload-envelope',
        ]);
        $draft = DocumentSigningRequest::create([
            'client_id' => $client->id,
            'title' => 'Unsent Agreement',
            'status' => DocumentSigningRequest::STATUS_DRAFT,
        ]);

        $this->withSession(['user' => ['id' => $admin->id, 'accounttype' => 'admin']])
            ->get('/admin/document-signing')
            ->assertOk()
            ->assertSee('status-sent')
            ->assertDontSee('Sync status');
        $this->assertNotNull($sent->fresh()->last_synced_at);

        $this->get('/admin/document-signing')
            ->assertOk()
            ->assertSee('status-completed')
            ->assertDontSee('/admin/document-signing/' . $sent->id . '/void');
        $this->assertSame(DocumentSigningRequest::STATUS_COMPLETED, $sent->fresh()->status);
        $signedAt = $sent->fresh()->signed_at;
        $this->assertNotNull($signedAt);

        $this->get('/admin/document-signing')->assertOk()->assertSee('status-completed');
        $this->assertTrue($signedAt->equalTo($sent->fresh()->signed_at));
        $this->assertNull($draft->fresh()->last_synced_at);
        Http::assertSentCount(4);
    }

    public function test_failed_automatic_sync_keeps_page_available_and_other_requests_sync(): void
    {
        $this->configureDocuSign();
        Http::preventStrayRequests();
        Http::fake([
            'account-d.docusign.com/oauth/token' => Http::response(['access_token' => 'test-token']),
            'demo.docusign.net/restapi/v2.1/accounts/account-id/envelopes/unavailable-envelope' => Http::response(['message' => 'Unavailable'], 503),
            'demo.docusign.net/restapi/v2.1/accounts/account-id/envelopes/healthy-envelope' => Http::response(['status' => 'voided']),
        ]);
        $admin = User::factory()->create(['accounttype' => 'admin']);
        $client = $this->createClient('failure@example.com');
        $failed = DocumentSigningRequest::create([
            'client_id' => $client->id,
            'title' => 'Unavailable Agreement',
            'status' => DocumentSigningRequest::STATUS_SENT,
            'docusign_envelope_id' => 'unavailable-envelope',
        ]);
        $healthy = DocumentSigningRequest::create([
            'client_id' => $client->id,
            'title' => 'Available Agreement',
            'status' => DocumentSigningRequest::STATUS_SENT,
            'docusign_envelope_id' => 'healthy-envelope',
        ]);

        $this->withSession(['user' => ['id' => $admin->id, 'accounttype' => 'admin']])
            ->get('/admin/document-signing')
            ->assertOk()
            ->assertSee('Some signing statuses could not be refreshed')
            ->assertSee('Unavailable Agreement')
            ->assertSee('status-voided');

        $this->assertSame(DocumentSigningRequest::STATUS_SENT, $failed->fresh()->status);
        $this->assertNull($failed->fresh()->last_synced_at);
        $this->assertSame(DocumentSigningRequest::STATUS_VOIDED, $healthy->fresh()->status);
        Http::assertSentCount(3);
    }

    public function test_unconfigured_docusign_does_not_attempt_automatic_sync(): void
    {
        config(['services.docusign.integration_key' => null]);
        Http::fake();
        $admin = User::factory()->create(['accounttype' => 'admin']);
        $client = $this->createClient('unconfigured@example.com');
        $document = DocumentSigningRequest::create([
            'client_id' => $client->id,
            'title' => 'Saved Agreement',
            'status' => DocumentSigningRequest::STATUS_SENT,
            'docusign_envelope_id' => 'saved-envelope',
        ]);

        $this->withSession(['user' => ['id' => $admin->id, 'accounttype' => 'admin']])
            ->get('/admin/document-signing')
            ->assertOk()
            ->assertSee('Saved Agreement')
            ->assertSee('DocuSign API is not fully configured');

        $this->assertNull($document->fresh()->last_synced_at);
        Http::assertNothingSent();
    }

    public function test_admin_gets_actionable_message_when_docusign_consent_is_required(): void
    {
        config([
            'services.docusign.demo' => true,
            'services.docusign.integration_key' => 'integration-key',
            'services.docusign.user_id' => 'user-id',
            'services.docusign.private_key' => $this->privateKey(),
            'services.docusign.account_id' => 'account-id',
            'services.docusign.base_path' => 'https://demo.docusign.net/restapi',
            'services.docusign.oauth_base_path' => 'account-d.docusign.com',
            'services.docusign.consent_redirect_uri' => 'http://localhost',
        ]);

        Http::fake([
            'account-d.docusign.com/oauth/token' => Http::response(['error' => 'consent_required'], 400),
        ]);

        Storage::fake('local');
        Storage::disk('local')->put('document-signing/1/agreement.pdf', 'fake pdf contents');

        $admin = User::factory()->create([
            'accounttype' => 'admin',
        ]);

        $client = $this->createClient('kim@example.com');
        $signingRequest = DocumentSigningRequest::create([
            'client_id' => $client->id,
            'created_by' => $admin->id,
            'title' => 'Service Agreement',
            'document_path' => 'document-signing/1/agreement.pdf',
            'document_original_name' => 'agreement.pdf',
            'status' => DocumentSigningRequest::STATUS_DRAFT,
        ]);

        $response = $this->withSession([
            'user' => ['id' => $admin->id, 'accounttype' => 'admin'],
        ])->post('/admin/document-signing/' . $signingRequest->id . '/send');

        $response->assertRedirect('/admin/document-signing')
            ->assertSessionHas('error', fn ($message) => str_contains($message, 'DocuSign JWT consent is required'))
            ->assertSessionHas('docusign_consent_url', fn ($url) => str_contains($url, 'scope=signature%20impersonation'));

        $this->assertSame(DocumentSigningRequest::STATUS_FAILED, $signingRequest->fresh()->status);
    }

    public function test_sent_envelopes_request_docusign_email_for_embedded_signers(): void
    {
        $this->configureDocuSign();

        Http::fake([
            'account-d.docusign.com/oauth/token' => Http::response(['access_token' => 'test-token'], 200),
            'demo.docusign.net/restapi/v2.1/accounts/account-id/envelopes' => Http::response([
                'envelopeId' => 'envelope-123',
                'status' => 'sent',
            ], 201),
        ]);

        Storage::fake('local');
        Storage::disk('local')->put('document-signing/1/agreement.pdf', 'fake pdf contents');

        $admin = User::factory()->create([
            'accounttype' => 'admin',
        ]);

        $client = $this->createClient('kim@example.com');
        $signingRequest = DocumentSigningRequest::create([
            'client_id' => $client->id,
            'created_by' => $admin->id,
            'title' => 'Service Agreement',
            'document_path' => 'document-signing/1/agreement.pdf',
            'document_original_name' => 'agreement.pdf',
            'status' => DocumentSigningRequest::STATUS_DRAFT,
        ]);

        $response = $this->withSession([
            'user' => ['id' => $admin->id, 'accounttype' => 'admin'],
        ])->post('/admin/document-signing/' . $signingRequest->id . '/send');

        $response->assertRedirect('/admin/document-signing');

        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/envelopes')
            && data_get($request->data(), 'notification.useAccountDefaults') === false
            && data_get($request->data(), 'notification.expirations.expireEnabled') === true
            && data_get($request->data(), 'notification.expirations.expireAfter') === '10'
            && data_get($request->data(), 'notification.expirations.expireWarn') === '1'
            && data_get($request->data(), 'recipients.signers.0.clientUserId') === 'client-' . $client->id
            && data_get($request->data(), 'recipients.signers.0.embeddedRecipientStartURL') === 'SIGN_AT_DOCUSIGN');

        $this->assertSame('envelope-123', $signingRequest->fresh()->docusign_envelope_id);
    }

    public function test_template_envelopes_expire_ten_days_after_sending(): void
    {
        $this->configureDocuSign();
        Http::preventStrayRequests();
        Http::fake([
            'account-d.docusign.com/oauth/token' => Http::response(['access_token' => 'test-token']),
            'demo.docusign.net/restapi/v2.1/accounts/account-id/envelopes' => Http::response([
                'envelopeId' => 'template-envelope',
                'status' => 'sent',
            ], 201),
        ]);

        $admin = User::factory()->create(['accounttype' => 'admin']);
        $signingRequest = DocumentSigningRequest::create([
            'client_id' => $this->createClient('template-expiration@example.com')->id,
            'title' => 'Template agreement',
            'docusign_template_id' => 'template-id',
            'status' => DocumentSigningRequest::STATUS_DRAFT,
            'created_at' => now()->subDays(5),
        ]);

        $this->withSession(['user' => ['id' => $admin->id, 'accounttype' => 'admin']])
            ->post('/admin/document-signing/' . $signingRequest->id . '/send')
            ->assertRedirect('/admin/document-signing')->assertSessionHasNoErrors();

        Http::assertSent(fn ($request) => $request->method() === 'POST'
            && str_ends_with($request->url(), '/envelopes')
            && $request['templateId'] === 'template-id'
            && $request['status'] === 'sent'
            && data_get($request->data(), 'notification.useAccountDefaults') === false
            && data_get($request->data(), 'notification.expirations.expireEnabled') === true
            && data_get($request->data(), 'notification.expirations.expireAfter') === '10'
            && data_get($request->data(), 'notification.expirations.expireWarn') === '1');
        $this->assertSame(DocumentSigningRequest::STATUS_SENT, $signingRequest->fresh()->status);
    }

    public function test_admin_can_upload_multiple_documents_for_one_signing_request(): void
    {
        $admin = User::factory()->create([
            'accounttype' => 'admin',
        ]);

        $client = $this->createClient('kim@example.com');

        $response = $this->withSession([
            'user' => ['id' => $admin->id, 'accounttype' => 'admin'],
        ])->post('/admin/document-signing', [
            'client_id' => $client->id,
            'title' => 'Onboarding Pack',
            'documents' => [
                UploadedFile::fake()->create('service-agreement.pdf', 12, 'application/pdf'),
                UploadedFile::fake()->create('consent-form.pdf', 8, 'application/pdf'),
            ],
            'signature_positions' => [
                ['page' => 1, 'x' => 100, 'y' => 200],
                ['page' => 2, 'x' => 300, 'y' => 400],
            ],
        ]);

        $response->assertRedirect('/admin/document-signing');

        $signingRequest = DocumentSigningRequest::first();

        $this->assertSame('service-agreement.pdf', $signingRequest->document_original_name);
        $this->assertSame([
            'service-agreement.pdf',
            'consent-form.pdf',
        ], $signingRequest->documentDisplayNames());
        $this->assertSame(100, $signingRequest->document_files[0]['signature_x']);
        $this->assertSame(400, $signingRequest->document_files[1]['signature_y']);
    }

    public function test_multiple_uploaded_documents_are_sent_in_one_docusign_envelope(): void
    {
        $this->configureDocuSign();

        Http::fake([
            'account-d.docusign.com/oauth/token' => Http::response(['access_token' => 'test-token'], 200),
            'demo.docusign.net/restapi/v2.1/accounts/account-id/envelopes' => Http::response([
                'envelopeId' => 'envelope-456',
                'status' => 'sent',
            ], 201),
        ]);

        Storage::fake('local');
        Storage::disk('local')->put('document-signing/1/service-agreement.pdf', 'fake pdf contents');
        Storage::disk('local')->put('document-signing/1/consent-form.pdf', 'fake consent contents');

        $admin = User::factory()->create([
            'accounttype' => 'admin',
        ]);

        $client = $this->createClient('kim@example.com');
        $signingRequest = DocumentSigningRequest::create([
            'client_id' => $client->id,
            'created_by' => $admin->id,
            'title' => 'Onboarding Pack',
            'document_path' => 'document-signing/1/service-agreement.pdf',
            'document_original_name' => 'service-agreement.pdf',
            'document_files' => [
                [
                    'path' => 'document-signing/1/service-agreement.pdf',
                    'original_name' => 'service-agreement.pdf',
                    'signature_page' => 1,
                    'signature_x' => 100,
                    'signature_y' => 200,
                ],
                [
                    'path' => 'document-signing/1/consent-form.pdf',
                    'original_name' => 'consent-form.pdf',
                    'signature_page' => 2,
                    'signature_x' => 300,
                    'signature_y' => 400,
                ],
            ],
            'status' => DocumentSigningRequest::STATUS_DRAFT,
        ]);

        $response = $this->withSession([
            'user' => ['id' => $admin->id, 'accounttype' => 'admin'],
        ])->post('/admin/document-signing/' . $signingRequest->id . '/send');

        $response->assertRedirect('/admin/document-signing');

        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/envelopes')
            && count(data_get($request->data(), 'documents', [])) === 2
            && data_get($request->data(), 'documents.0.documentId') === '1'
            && data_get($request->data(), 'documents.1.documentId') === '2'
            && data_get($request->data(), 'recipients.signers.0.tabs.signHereTabs.0.xPosition') === '100'
            && data_get($request->data(), 'recipients.signers.0.tabs.signHereTabs.1.pageNumber') === '2'
            && data_get($request->data(), 'recipients.signers.0.tabs.signHereTabs.1.yPosition') === '400');

        $this->assertSame('envelope-456', $signingRequest->fresh()->docusign_envelope_id);
    }

    public function test_admin_downloads_completed_envelope_as_signed_pdf_instead_of_source_files(): void
    {
        $this->configureDocuSign();
        Storage::fake('local');
        Http::preventStrayRequests();
        $pdf = "%PDF-1.7\nSigned agreement and consent\n%%EOF";
        Http::fake([
            'account-d.docusign.com/oauth/token' => Http::response(['access_token' => 'test-token']),
            '*/envelopes/completed-envelope/documents/combined*' => Http::response($pdf, 200, ['Content-Type' => 'application/pdf']),
            '*/envelopes/completed-envelope' => Http::response(['status' => 'completed']),
        ]);
        $admin = User::factory()->create(['accounttype' => 'admin']);
        $signingRequest = DocumentSigningRequest::create([
            'client_id' => $this->createClient('signed@example.com')->id,
            'title' => 'Agreement and consent',
            'status' => DocumentSigningRequest::STATUS_COMPLETED,
            'docusign_envelope_id' => 'completed-envelope',
            'document_files' => [
                ['path' => 'missing-agreement.docx', 'original_name' => 'agreement.docx'],
                ['path' => 'missing-consent.pdf', 'original_name' => 'consent.pdf'],
            ],
        ]);

        $this->withSession(['user' => ['id' => $admin->id, 'accounttype' => 'admin']]);
        $this->get('/admin/document-signing')->assertOk()
            ->assertSee('Download signed document')
            ->assertSee('/admin/document-signing/' . $signingRequest->id . '/download')
            ->assertDontSee('/admin/document-signing/' . $signingRequest->id . '/download/0');

        // Old indexed download links must also return the signed envelope.
        foreach (['', '/0', '/1'] as $suffix) {
            $this->get('/admin/document-signing/' . $signingRequest->id . '/download' . $suffix)
                ->assertOk()
                ->assertDownload(strtolower($signingRequest->displayReference()) . '-signed.pdf')
                ->assertHeader('Content-Type', 'application/pdf')
                ->assertStreamedContent($pdf);
        }

        Http::assertSent(fn ($request) => $request->method() === 'GET'
            && str_contains($request->url(), '/restapi/v2.1/accounts/account-id/envelopes/completed-envelope/documents/combined')
            && $request['certificate'] === 'true'
            && $request->hasHeader('Accept', 'application/pdf')
            && $request->hasHeader('Authorization', 'Bearer test-token'));
    }

    public function test_client_can_download_signed_template_envelope_without_local_files(): void
    {
        $this->configureDocuSign();
        Http::preventStrayRequests();
        $pdf = "%PDF-1.7\nSigned template\n%%EOF";
        Http::fake([
            'account-d.docusign.com/oauth/token' => Http::response(['access_token' => 'test-token']),
            '*/envelopes/template-envelope/documents/combined*' => Http::response($pdf, 200, ['Content-Type' => 'application/pdf']),
            '*/envelopes/template-envelope' => Http::response(['status' => 'completed']),
        ]);
        $client = $this->createClient('template@example.com');
        $user = User::factory()->create(['accounttype' => 'client', 'record_id' => $client->id]);
        $signingRequest = DocumentSigningRequest::create([
            'client_id' => $client->id,
            'title' => 'Template agreement',
            'status' => DocumentSigningRequest::STATUS_COMPLETED,
            'docusign_template_id' => 'template-id',
            'docusign_envelope_id' => 'template-envelope',
        ]);

        $this->withSession(['user' => ['id' => $user->id, 'accounttype' => 'client']]);
        $this->get('/client/document-signing')->assertOk()->assertSee('Download signed document');
        $this->get('/client/document-signing/' . $signingRequest->id . '/download')
            ->assertOk()->assertStreamedContent($pdf);
    }

    public function test_client_cannot_download_another_clients_signed_document(): void
    {
        Http::fake();
        $client = $this->createClient('owner@example.com');
        $otherClient = $this->createClient('other@example.com');
        $user = User::factory()->create(['accounttype' => 'client', 'record_id' => $otherClient->id]);
        $signingRequest = DocumentSigningRequest::create([
            'client_id' => $client->id,
            'title' => 'Private agreement',
            'status' => DocumentSigningRequest::STATUS_COMPLETED,
            'docusign_envelope_id' => 'private-envelope',
        ]);

        $this->withSession(['user' => ['id' => $user->id, 'accounttype' => 'client']])
            ->get('/client/document-signing/' . $signingRequest->id . '/download')->assertNotFound();
        Http::assertNothingSent();
    }

    public function test_incomplete_requests_still_download_the_selected_original_file(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('agreement.pdf', 'Original agreement');
        Storage::disk('local')->put('consent.docx', 'Original consent');
        Http::fake();
        $admin = User::factory()->create(['accounttype' => 'admin']);
        $signingRequest = DocumentSigningRequest::create([
            'client_id' => $this->createClient('pending@example.com')->id,
            'title' => 'Pending agreement',
            'status' => DocumentSigningRequest::STATUS_SENT,
            'document_files' => [
                ['path' => 'agreement.pdf', 'original_name' => 'agreement.pdf'],
                ['path' => 'consent.docx', 'original_name' => 'consent.docx'],
            ],
        ]);

        $this->withSession(['user' => ['id' => $admin->id, 'accounttype' => 'admin']])
            ->get('/admin/document-signing/' . $signingRequest->id . '/download/1')
            ->assertOk()->assertDownload('consent.docx')->assertStreamedContent('Original consent');
        Http::assertNothingSent();
    }

    public function test_signed_download_failures_never_fall_back_to_unsigned_original(): void
    {
        $this->configureDocuSign();
        Storage::fake('local');
        Storage::disk('local')->put('agreement.pdf', 'Unsigned original');
        Http::preventStrayRequests();
        Http::fake([
            'account-d.docusign.com/oauth/token' => Http::response(['access_token' => 'test-token']),
            '*/documents/combined*' => Http::sequence()
                ->push(['errorCode' => 'DOCUMENT_NOT_FOUND'], 404)
                ->push(['errorCode' => 'AUTHORIZATION_INVALID_TOKEN'], 401)
                ->push('Service unavailable', 503)
                ->push('', 200)
                ->push(['error' => 'Unexpected JSON'], 200),
        ]);
        $admin = User::factory()->create(['accounttype' => 'admin']);
        $signingRequest = DocumentSigningRequest::create([
            'client_id' => $this->createClient('failure@example.com')->id,
            'title' => 'Completed agreement',
            'status' => DocumentSigningRequest::STATUS_COMPLETED,
            'docusign_envelope_id' => 'completed-envelope',
            'document_path' => 'agreement.pdf',
        ]);

        $this->withSession(['user' => ['id' => $admin->id, 'accounttype' => 'admin']]);
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->get('/admin/document-signing/' . $signingRequest->id . '/download')
                ->assertRedirect('/admin/document-signing')
                ->assertSessionHas('error', 'Unable to download the signed document from DocuSign. Please try again later.');
        }
        $this->assertSame(DocumentSigningRequest::STATUS_COMPLETED, $signingRequest->fresh()->status);
    }

    public function test_completed_request_without_envelope_id_does_not_download_original(): void
    {
        $this->configureDocuSign();
        Storage::fake('local');
        Storage::disk('local')->put('agreement.pdf', 'Unsigned original');
        Http::fake();
        $admin = User::factory()->create(['accounttype' => 'admin']);
        $signingRequest = DocumentSigningRequest::create([
            'client_id' => $this->createClient('missing-envelope@example.com')->id,
            'title' => 'Completed agreement',
            'status' => DocumentSigningRequest::STATUS_COMPLETED,
            'document_path' => 'agreement.pdf',
        ]);

        $this->withSession(['user' => ['id' => $admin->id, 'accounttype' => 'admin']])
            ->get('/admin/document-signing/' . $signingRequest->id . '/download')
            ->assertRedirect('/admin/document-signing')->assertSessionHas('error');
        Http::assertNothingSent();
    }

    protected function createClient(string $email): Client
    {
        return Client::create([
            'first_name' => 'Kim',
            'last_name' => 'Ramirez',
            'email' => $email,
            'phone' => '0400000000',
            'date_of_birth' => '1990-01-01',
            'address' => '1 Main Street',
            'city' => 'Sydney',
            'state' => 'NSW',
            'zip_code' => '2000',
            'country' => 'Australia',
        ]);
    }

    protected function configureDocuSign(): void
    {
        config([
            'services.docusign.demo' => true,
            'services.docusign.integration_key' => 'integration-key',
            'services.docusign.user_id' => 'user-id',
            'services.docusign.private_key' => $this->privateKey(),
            'services.docusign.account_id' => 'account-id',
            'services.docusign.base_path' => 'https://demo.docusign.net/restapi',
            'services.docusign.oauth_base_path' => 'account-d.docusign.com',
            'services.docusign.consent_redirect_uri' => 'http://localhost',
            'services.docusign.send_email_to_embedded_signers' => true,
        ]);
    }

    protected function privateKey(): string
    {
        return <<<'KEY'
-----BEGIN PRIVATE KEY-----
MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCyCE96EGYDZ4WG
40VHkC6sSv/VZmSax0F2jsbqJYxon8eJ2qg+F1oC+cXdQNu3MvzPX4zWPSAHlEeH
XMnoILT9LP6nbQWnwj7VCvIVCZqN/ln9HkU/VxTzceuPknZ19yf6B02KiVv4vfeQ
rr9gILWuVuvJjFmmIoJIabvvgwl29SUDs4gKVfNPLkbDiRXP/BZGQZnlgsfdz2DX
N1ZasYgWJ4q/rZKwwAY+YhJJpZGfp6yUHO00WjI10DXwt8b4KliRbaByXaLWqf/r
x8pgSAlsT1UXQgHq6/cILbjpj6TUtSN7YP6egeMNrEChZOCrVk53JkaONKcs6z3W
QbDIlW9tAgMBAAECggEANSnjJeXQuNKInruufAzTyzKITD8L6uZ2p4NCZjh5QeL4
0LNBi20ovXu9+PC9y+rHcNutMGrHbkv6wjLUvmNJ+NKfZvrY9fw9DBx9Jwgbpaxf
TgVkk2IbSQlrWQ/ZHSgFwZW9s5GKK/D6hTORxZBvAJVHSwQE32YttpcQfpkIUgOq
FbGh2lSWl9u3PlnjdX0ZTlymTaR75aRJHl8di9smKjF2wJW7GzVX3Wjdm65Qa1U6
rJ6qTel9XLsj1GxNu2VqaAQEZY0Nv/J86x7rUSW8uXIZ2luySJlUKTOQr7YI5cOQ
VnOLMuR5PvhawbRpuUk0ihAFIHXa1Y/OrTtr6ZkjVwKBgQDaP0oO8ThyVslIsRdr
UoymdOZEHidlZpgwRRr9eTqsttvw3X4fibOQPX9HLhdcKGtUT0V/uxLTGhraGa6h
GZ5MHtw3xhrQgi8QGWfx81CVvn2qMvNY2xDj4sN0a2EJyNDuN+6keEY+cPJa15Hd
OK2nQW3N2Oh4p7nwvCsJCpfH1wKBgQDQ1C2e5wXNW2MFv+owf75KjQDHwWUVZ4XL
yxg7/EBePFrBjP8COc6vdUOGyRF7JVc52ennySqnw2YvjBSSc4zAQ+PH0Oa8DZ4o
rt5OUrMNr0lO5p2LATJ3CBdeZWJg5C9RPJ64e7Hi2Gtqa+hbHRJB9cfCuGKKKOKh
I8Rf2WUKWwKBgQCsTteLoIsuSrK0P4z74LrHGPhLw3rbJfr18Tlz8eiPP/e53UWc
ZL1uHf+FFTDAQ/EpKUT3qZ0dKjkVBL3I108RTjQTnMbC5GV/0m8sqStwbmsMmxlh
h+N36CrkmXqNnQzHZ0V0eLkMx2OwsQ2qmPsRWCj3Ik+4IsQ8+nMHumNHFQKBgHO2
PhkgdcyWaMYU01VQU0bH3rjQ5ov3suF7hdAnNBuDYd6BnAPFuErstSAIZdFQnYaG
wt8WTrZqxUJvYi7MLB+U/rAHhvKIsSxlyPSpSmG40OwRSRZu8WGXcCt0DhEWH0nq
RyB+GdFSjiKH7FYgQ6+c2rWAqclYYktboEJkGWKlAoGBAK8uH10XLIHlMxF2L00f
V1Koc6Pit9S5isczZagY6ktgS0ERL7voOLzAtrrxdc52rVZPgDyxsFlgO2vg/Jtx
vH0MBtr1OuskC7eqFIdB337iUfmYOqhu1uCv8G5wAQn1a0KePrW5u5l2xTkESAy0
PaWVp6fuM1ez29MSFttDvOec
-----END PRIVATE KEY-----
KEY;
    }
}

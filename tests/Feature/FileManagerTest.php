<?php

namespace Tests\Feature;

use App\Services\GoogleDriveService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FileManagerTest extends TestCase
{
    private string $credentialsPath;

    private string $credentials;

    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
        $this->credentialsPath = tempnam(sys_get_temp_dir(), 'drive-test-');
        $this->credentials = json_encode([
            'type' => 'service_account', 'client_email' => 'test@example.iam.gserviceaccount.com',
            'private_key' => 'fake-key-not-used-with-cached-token',
        ]);
        file_put_contents($this->credentialsPath, $this->credentials);
        config(['services.google_drive.shared_drive_id' => 'drive-1',
            'services.google_drive.credentials_path' => $this->credentialsPath,
            'services.google_drive.max_upload_mb' => 20]);
        Cache::put('google-drive-token:'.hash('sha256', $this->credentials), 'test-token', 3600);
    }

    protected function tearDown(): void
    {
        if (isset($this->credentialsPath) && is_file($this->credentialsPath)) {
            unlink($this->credentialsPath);
        }
        parent::tearDown();
    }

    private function admin(): static
    {
        return $this->withSession(['user' => ['id' => 1, 'accounttype' => 'admin']]);
    }

    private function item(string $id, string $mime = GoogleDriveService::FOLDER, array $extra = []): array
    {
        return array_replace([
            'id' => $id, 'name' => $id === 'drive-1' ? 'Our Care Shared Drive' : $id,
            'driveId' => 'drive-1', 'mimeType' => $mime, 'trashed' => false,
            'parents' => $id === 'drive-1' ? [] : ['drive-1'],
            'capabilities' => ['canAddChildren' => true, 'canEdit' => true, 'canTrash' => true, 'canDownload' => true, 'canMoveItemWithinDrive' => true],
        ], $extra);
    }

    private function fakeDrive(array $items = [], array $listing = []): void
    {
        $root = $this->item('drive-1');
        Http::fake(function ($request) use ($items, $listing, $root) {
            $path = parse_url($request->url(), PHP_URL_PATH);
            if ($request->method() !== 'GET') {
                return Http::response(['id' => 'new-item']);
            }
            if ($path === '/drive/v3/files') {
                return Http::response(['files' => $listing, 'nextPageToken' => 'next-page']);
            }
            $id = basename($path);

            return Http::response($id === 'drive-1' ? $root : ($items[$id] ?? []));
        });
    }

    public function test_guests_and_non_admins_cannot_access_any_file_manager_endpoint(): void
    {
        $this->get('/admin/file-manager')->assertRedirect('/login');
        foreach (['client', 'healthcare_worker', 'unexpected-role'] as $role) {
            $this->withSession(['user' => ['id' => 2, 'accounttype' => $role]]);
            $this->get('/admin/file-manager')->assertForbidden();
            $this->post('/admin/file-manager/folders')->assertForbidden();
            $this->post('/admin/file-manager/upload')->assertForbidden();
            $this->patch('/admin/file-manager/files/file-1')->assertForbidden();
            $this->delete('/admin/file-manager/files/file-1')->assertForbidden();
            $this->get('/admin/file-manager/files/file-1/download')->assertForbidden();
        }
        Http::assertNothingSent();
    }

    public function test_unconfigured_page_shows_setup_state_without_api_calls(): void
    {
        config(['services.google_drive.shared_drive_id' => null]);
        $this->admin()->get('/admin/file-manager')->assertOk()->assertSee('Connect your Google Shared Drive');
        Http::assertNothingSent();
    }

    public function test_listing_renders_breadcrumbs_and_escapes_names_and_search_queries(): void
    {
        $folder = $this->item('folder-1', extra: ['name' => 'Documents']);
        $file = $this->item('file-1', 'application/pdf', ['name' => '<script>alert(1)</script>.pdf', 'size' => '1024']);
        $this->fakeDrive(['folder-1' => $folder], [$file]);
        $this->admin()->get('/admin/file-manager?'.http_build_query(['folder' => 'folder-1', 'search' => "O'Brien", 'page' => 'page-2']))
            ->assertOk()->assertSee('Our Care Shared Drive')->assertSee('Documents')
            ->assertSee('&lt;script&gt;', false)->assertDontSee('<script>alert(1)</script>', false)->assertSee('Next page');
        Http::assertSent(fn ($request) => str_contains($request->url(), '/files?')
            && $request['corpora'] === 'drive' && $request['driveId'] === 'drive-1'
            && $request['supportsAllDrives'] === 'true' && $request['includeItemsFromAllDrives'] === 'true'
            && $request['pageToken'] === 'page-2' && str_contains($request['q'], "O\\'Brien"));
    }

    public function test_folder_creation_sets_shared_drive_parent(): void
    {
        $this->fakeDrive();
        $this->admin()->post('/admin/file-manager/folders', ['folder' => 'drive-1', 'name' => 'Client documents'])
            ->assertRedirect('/admin/file-manager?folder=drive-1')->assertSessionHas('success');
        Http::assertSent(fn ($request) => $request->method() === 'POST'
            && $request['parents'] === ['drive-1'] && $request['mimeType'] === GoogleDriveService::FOLDER);
    }

    public function test_files_outside_the_drive_cannot_be_read_or_changed(): void
    {
        $this->fakeDrive(['outside' => $this->item('outside', 'application/pdf', ['driveId' => 'other-drive'])]);
        $this->admin()->get('/admin/file-manager/files/outside/download')->assertNotFound();
        $this->patch('/admin/file-manager/files/outside', ['folder' => 'drive-1', 'name' => 'New'])->assertNotFound();
        $this->delete('/admin/file-manager/files/outside', ['folder' => 'drive-1'])->assertNotFound();
        $this->post('/admin/file-manager/folders', ['folder' => 'outside', 'name' => 'New'])->assertNotFound();
        Http::assertNotSent(fn ($request) => $request->method() !== 'GET');
    }

    public function test_root_drive_cannot_be_renamed_or_trashed(): void
    {
        $this->admin()->patch('/admin/file-manager/files/drive-1', ['folder' => 'drive-1', 'name' => 'New'])->assertForbidden();
        $this->delete('/admin/file-manager/files/drive-1', ['folder' => 'drive-1'])->assertForbidden();
        Http::assertNothingSent();
    }

    public function test_move_updates_parents_and_rejects_descendants(): void
    {
        $this->fakeDrive([
            'source' => $this->item('source'), 'target' => $this->item('target'),
            'child' => $this->item('child', extra: ['parents' => ['source']]),
        ]);
        $this->admin()->patch('/admin/file-manager/files/source', ['folder' => 'drive-1', 'destination' => 'child'])->assertUnprocessable();
        Http::assertNotSent(fn ($request) => $request->method() === 'PATCH');
        $this->patch('/admin/file-manager/files/source', ['folder' => 'drive-1', 'destination' => 'target'])->assertSessionHas('success');
        Http::assertSent(fn ($request) => $request->method() === 'PATCH'
            && str_contains($request->url(), 'addParents=target') && str_contains($request->url(), 'removeParents=drive-1'));
    }

    public function test_rename_and_trash_use_metadata_updates_not_permanent_delete(): void
    {
        $this->fakeDrive(['file-1' => $this->item('file-1', 'application/pdf')]);
        $this->admin()->patch('/admin/file-manager/files/file-1', ['folder' => 'drive-1', 'name' => 'Renamed.pdf'])->assertSessionHas('success');
        $this->delete('/admin/file-manager/files/file-1', ['folder' => 'drive-1'])->assertSessionHas('success');
        Http::assertSent(fn ($request) => $request->method() === 'PATCH' && ($request['name'] ?? null) === 'Renamed.pdf');
        Http::assertSent(fn ($request) => $request->method() === 'PATCH' && ($request['trashed'] ?? null) === true);
        Http::assertNotSent(fn ($request) => $request->method() === 'DELETE');
    }

    public function test_read_only_permissions_block_mutations(): void
    {
        $this->fakeDrive(['readonly' => $this->item('readonly', extra: ['capabilities' => []])]);
        $this->admin()->post('/admin/file-manager/folders', ['folder' => 'readonly', 'name' => 'New'])->assertForbidden();
        $this->patch('/admin/file-manager/files/readonly', ['folder' => 'drive-1', 'name' => 'New'])->assertForbidden();
        $this->delete('/admin/file-manager/files/readonly', ['folder' => 'drive-1'])->assertForbidden();
        Http::assertNotSent(fn ($request) => $request->method() !== 'GET');
    }

    public function test_resumable_upload_streams_content_to_google(): void
    {
        $uploadedBody = null;
        Http::fake([
            'www.googleapis.com/drive/v3/files/drive-1*' => Http::response($this->item('drive-1')),
            'www.googleapis.com/upload/drive/v3/files?uploadType=resumable*' => Http::response([], 200, ['Location' => 'https://www.googleapis.com/upload/drive/v3/files?upload_id=test']),
            'www.googleapis.com/upload/drive/v3/files?upload_id=test' => function ($request) use (&$uploadedBody) {
                $uploadedBody = $request->body();

                return Http::response(['id' => 'uploaded']);
            },
        ]);
        $this->admin()->post('/admin/file-manager/upload', ['folder' => 'drive-1', 'file' => UploadedFile::fake()->createWithContent('notes.txt', 'Care notes')])->assertSessionHas('success');
        Http::assertSent(fn ($request) => $request->method() === 'POST' && $request['parents'] === ['drive-1'] && $request['name'] === 'notes.txt');
        $this->assertSame('Care notes', $uploadedBody);
    }

    public function test_upload_size_and_names_are_validated_before_api_calls(): void
    {
        config(['services.google_drive.max_upload_mb' => 1]);
        $this->admin()->post('/admin/file-manager/upload', ['folder' => 'drive-1', 'file' => UploadedFile::fake()->create('large.pdf', 2048)])->assertSessionHasErrors('file');
        $this->post('/admin/file-manager/folders', ['folder' => 'drive-1', 'name' => '   '])->assertSessionHasErrors('name');
        $this->post('/admin/file-manager/folders', ['folder' => "bad'id", 'name' => 'New'])->assertSessionHasErrors('folder');
        $this->post('/admin/file-manager/upload', ['folder' => 'drive-1', 'file' => 'not-an-upload'])->assertSessionHasErrors('file');
        $this->post('/admin/file-manager/upload', ['folder' => 'drive-1', 'file' => UploadedFile::fake()->createWithContent('empty.txt', '')])->assertSessionHasErrors('file');
        Http::assertNothingSent();
    }

    public function test_downloads_stream_original_files_and_export_google_documents(): void
    {
        Http::fake(function ($request) {
            if (str_contains($request->url(), 'alt=media') || str_contains($request->url(), '/export')) {
                return Http::response('document content', 200, ['Content-Type' => 'application/pdf']);
            }
            $id = basename(parse_url($request->url(), PHP_URL_PATH));

            return Http::response($this->item($id, $id === 'doc' ? 'application/vnd.google-apps.document' : 'application/pdf', ['name' => $id === 'doc' ? 'Agreement' : 'Notes.pdf']));
        });
        $this->admin()->get('/admin/file-manager/files/file-1/download')->assertDownload('Notes.pdf')->assertStreamedContent('document content');
        $this->get('/admin/file-manager/files/doc/download')->assertDownload('Agreement.pdf')->assertStreamedContent('document content');
        Http::assertSent(fn ($request) => str_contains($request->url(), '/export') && $request['mimeType'] === 'application/pdf');
    }

    public function test_google_failures_are_readable_and_do_not_expose_provider_details(): void
    {
        Http::fake(['*' => Http::response(['error' => ['message' => 'SECRET PROVIDER DETAILS']], 403)]);
        $this->admin()->get('/admin/file-manager')->assertOk()->assertSee('Google Drive denied')->assertDontSee('SECRET PROVIDER DETAILS');
        $this->post('/admin/file-manager/folders', ['folder' => 'drive-1', 'name' => 'New'])->assertSessionHas('drive_error');
    }

    public function test_picker_lists_only_folders_and_returns_pagination(): void
    {
        $this->fakeDrive([], [$this->item('folder-1')]);
        $this->admin()->getJson('/admin/file-manager?picker=1')->assertOk()
            ->assertJsonPath('folder.id', 'drive-1')->assertJsonPath('nextPageToken', 'next-page');
        Http::assertSent(fn ($request) => str_contains($request->url(), '/files?') && str_contains($request['q'], "mimeType = 'application/vnd.google-apps.folder'"));
    }

    public function test_service_account_token_exchange_is_cached(): void
    {
        $options = ['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA];
        if (is_file('C:/xampp/php/extras/openssl/openssl.cnf')) {
            $options['config'] = 'C:/xampp/php/extras/openssl/openssl.cnf';
        }
        $key = openssl_pkey_new($options);
        $this->assertNotFalse($key);
        openssl_pkey_export($key, $privateKey, null, $options);
        $credentials = json_decode($this->credentials, true);
        $credentials['private_key'] = $privateKey;
        file_put_contents($this->credentialsPath, json_encode($credentials));
        Http::fake([
            'oauth2.googleapis.com/token' => Http::response(['access_token' => 'new-token', 'expires_in' => 3600, 'token_type' => 'Bearer']),
            'www.googleapis.com/drive/v3/files/drive-1*' => Http::response($this->item('drive-1')),
            'www.googleapis.com/drive/v3/files?*' => Http::response(['files' => []]),
        ]);
        $this->admin()->get('/admin/file-manager')->assertOk()->assertDontSee('authentication failed');
        $this->get('/admin/file-manager')->assertOk();
        $tokens = Http::recorded(fn ($request) => str_contains($request->url(), '/token'));
        $this->assertCount(1, $tokens);
        Http::assertSent(fn ($request) => str_contains($request->url(), '/drive/v3/') && $request->hasHeader('Authorization', 'Bearer new-token'));
    }
}

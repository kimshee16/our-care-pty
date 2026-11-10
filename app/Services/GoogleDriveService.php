<?php

namespace App\Services;

use App\Exceptions\GoogleDriveException;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Psr\Http\Message\RequestInterface;

class GoogleDriveService
{
    public const FOLDER = 'application/vnd.google-apps.folder';

    private const API = 'https://www.googleapis.com/drive/v3';

    private const FIELDS = 'id,name,mimeType,size,modifiedTime,parents,driveId,trashed,capabilities(canAddChildren,canEdit,canTrash,canDownload,canMoveItemWithinDrive)';

    public function rootId(): string
    {
        return (string) config('services.google_drive.shared_drive_id');
    }

    public function isConfigured(): bool
    {
        $path = config('services.google_drive.credentials_path');

        return $this->rootId() !== '' && is_string($path) && is_file($path) && is_readable($path);
    }

    public function maxUploadMb(): int
    {
        return max(1, min(100, (int) config('services.google_drive.max_upload_mb', 20)));
    }

    public function item(string $id): array
    {
        abort_unless(preg_match('/^[A-Za-z0-9_-]+$/D', $id), 404);
        $item = $this->request('GET', '/files/'.$id, ['fields' => self::FIELDS])->json();
        // Never let a submitted ID access another drive, even if the account can access it.
        abort_unless(($item['driveId'] ?? null) === $this->rootId() && empty($item['trashed']), 404);

        return $item;
    }

    public function folder(string $id): array
    {
        $folder = $this->item($id);
        abort_unless($folder['mimeType'] === self::FOLDER, 422, 'Choose a folder.');

        return $folder;
    }

    public function breadcrumbs(array $folder): array
    {
        $crumbs = [$folder];
        $seen = [$folder['id']];
        while ($folder['id'] !== $this->rootId()) {
            $parent = $folder['parents'][0] ?? null;
            abort_if(! $parent || in_array($parent, $seen, true) || count($seen) > 100, 422, 'Invalid folder hierarchy.');
            $folder = $this->folder($parent);
            array_unshift($crumbs, $folder);
            $seen[] = $parent;
        }

        return $crumbs;
    }

    public function listing(string $folder, string $search = '', ?string $page = null, bool $foldersOnly = false): array
    {
        $this->folder($folder);
        $query = "'".$folder."' in parents and trashed = false";
        if ($search !== '') {
            $escaped = str_replace(['\\', "'"], ['\\\\', "\\'"], $search);
            $query .= " and name contains '".$escaped."'";
        }
        if ($foldersOnly) {
            $query .= " and mimeType = '".self::FOLDER."'";
        }

        return $this->request('GET', '/files', array_filter([
            'corpora' => 'drive', 'driveId' => $this->rootId(), 'includeItemsFromAllDrives' => 'true',
            'q' => $query, 'pageSize' => 100, 'pageToken' => $page,
            'orderBy' => 'folder,name', 'fields' => 'nextPageToken,files('.self::FIELDS.')',
        ], fn ($value) => $value !== null))->json();
    }

    public function createFolder(string $parent, string $name): void
    {
        $folder = $this->folder($parent);
        $this->requireCapability($folder, 'canAddChildren');
        $this->request('POST', '/files', ['fields' => 'id'], [
            'name' => $name, 'mimeType' => self::FOLDER, 'parents' => [$parent],
        ]);
    }

    public function upload(string $parent, UploadedFile $file): void
    {
        $folder = $this->folder($parent);
        $this->requireCapability($folder, 'canAddChildren');
        // Stream a resumable upload so file contents aren't buffered in PHP memory.
        $session = $this->request('POST', '/files', ['uploadType' => 'resumable', 'fields' => 'id'], [
            'name' => $file->getClientOriginalName(), 'parents' => [$parent],
        ], 'https://www.googleapis.com/upload/drive/v3', [
            'X-Upload-Content-Type' => $file->getMimeType() ?: 'application/octet-stream',
            'X-Upload-Content-Length' => (string) $file->getSize(),
        ]);
        $location = $session->header('Location');
        $url = parse_url($location);
        if (! $url || ($url['scheme'] ?? '') !== 'https' || ($url['host'] ?? '') !== 'www.googleapis.com'
            || ! str_starts_with($url['path'] ?? '', '/upload/drive/')) {
            throw new GoogleDriveException('Google Drive could not start the upload. Please try again.');
        }
        $stream = fopen($file->getRealPath(), 'rb');
        try {
            $response = Http::withToken($this->accessToken())->connectTimeout(10)->timeout(180)
                ->withOptions(['allow_redirects' => false])
                ->withHeaders(['Content-Length' => (string) $file->getSize()])
                ->withBody($stream, $file->getMimeType() ?: 'application/octet-stream')->put($location);
            $this->checkResponse($response);
        } catch (ConnectionException $e) {
            throw new GoogleDriveException('The upload connection was interrupted. Refresh the folder before retrying.');
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }
    }

    public function update(string $id, ?string $name, ?string $destination): void
    {
        abort_if($id === $this->rootId(), 403, 'The shared drive itself cannot be changed.');
        $item = $this->item($id);
        $this->requireCapability($item, 'canEdit');
        $query = ['fields' => 'id'];
        if ($destination !== null && ! in_array($destination, $item['parents'] ?? [], true)) {
            $target = $this->folder($destination);
            $this->requireCapability($target, 'canAddChildren');
            $this->requireCapability($item, 'canMoveItemWithinDrive');
            $ancestors = array_column($this->breadcrumbs($target), 'id');
            abort_if(in_array($id, $ancestors, true), 422, 'A folder cannot be moved inside itself.');
            $query += ['addParents' => $destination, 'removeParents' => implode(',', $item['parents'] ?? [])];
        }
        $this->request('PATCH', '/files/'.$id, $query, $name !== null ? ['name' => $name] : new \stdClass);
    }

    public function trash(string $id): void
    {
        abort_if($id === $this->rootId(), 403, 'The shared drive itself cannot be trashed.');
        $item = $this->item($id);
        $this->requireCapability($item, 'canTrash');
        $this->request('PATCH', '/files/'.$id, ['fields' => 'id'], ['trashed' => true]);
    }

    public function download(string $id): array
    {
        $item = $this->item($id);
        $this->requireCapability($item, 'canDownload');
        abort_if($item['mimeType'] === self::FOLDER, 422, 'Folders cannot be downloaded.');
        $exports = [
            'application/vnd.google-apps.document' => ['application/pdf', '.pdf'],
            'application/vnd.google-apps.spreadsheet' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', '.xlsx'],
            'application/vnd.google-apps.presentation' => ['application/pdf', '.pdf'],
            'application/vnd.google-apps.drawing' => ['application/pdf', '.pdf'],
        ];
        $export = $exports[$item['mimeType']] ?? null;
        abort_if(str_starts_with($item['mimeType'], 'application/vnd.google-apps.') && ! $export, 422, 'This Google file type cannot be downloaded here.');
        $response = $this->request('GET', '/files/'.$id.($export ? '/export' : ''),
            $export ? ['mimeType' => $export[0]] : ['alt' => 'media'], null, self::API, [], true);

        return [$response->toPsrResponse()->getBody(), $item['name'].($export[1] ?? '')];
    }

    private function requireCapability(array $item, string $capability): void
    {
        abort_unless($item['capabilities'][$capability] ?? false, 403, 'Your Google Drive connection does not have permission for this action.');
    }

    private function request(string $method, string $path, array $query = [], array|object|null $body = null,
        string $base = self::API, array $headers = [], bool $stream = false): Response
    {
        try {
            $response = Http::withToken($this->accessToken())->acceptJson()->withHeaders($headers)
                ->connectTimeout(10)->timeout(60)->withOptions(['allow_redirects' => false])
                ->send($method, $base.$path, array_filter([
                    'query' => $query + ['supportsAllDrives' => 'true'], 'json' => $body, 'stream' => $stream,
                ], fn ($value) => $value !== null));
            $this->checkResponse($response);

            return $response;
        } catch (ConnectionException $e) {
            throw new GoogleDriveException('Google Drive could not be reached. Please try again.');
        }
    }

    private function checkResponse(Response $response): void
    {
        if ($response->successful()) {
            return;
        }
        throw new GoogleDriveException(match ($response->status()) {
            401 => 'Google Drive authentication expired or was rejected. Check the service account configuration.',
            403 => 'Google Drive denied this action. Check the service account’s Shared Drive membership and permissions.',
            404 => 'This file or folder is unavailable. Refresh the folder and try again.',
            429 => 'Google Drive is busy. Please wait a moment and try again.',
            default => 'Google Drive could not complete this action. Refresh the folder before retrying.',
        });
    }

    private function accessToken(): string
    {
        if (! $this->isConfigured()) {
            throw new GoogleDriveException('Google Drive is not connected. Ask your administrator to finish setup.');
        }
        $raw = file_get_contents(config('services.google_drive.credentials_path'));
        $credentials = json_decode($raw, true);
        if (! is_array($credentials) || ($credentials['type'] ?? '') !== 'service_account'
            || empty($credentials['client_email']) || empty($credentials['private_key'])) {
            throw new GoogleDriveException('The Google Drive service-account key is invalid.');
        }
        $key = 'google-drive-token:'.hash('sha256', $raw);
        if ($token = Cache::get($key)) {
            return $token;
        }
        if (! class_exists(ServiceAccountCredentials::class)) {
            throw new GoogleDriveException('The Google authentication library is missing on the hosting server. Deploy the Composer vendor dependencies, including google/auth.');
        }
        if (! extension_loaded('openssl')) {
            throw new GoogleDriveException('PHP OpenSSL is disabled on the hosting server. Ask your hosting provider to enable it for this website.');
        }
        try {
            $auth = new ServiceAccountCredentials('https://www.googleapis.com/auth/drive', $credentials);
            $token = $auth->fetchAuthToken(function (RequestInterface $request) {
                $response = Http::connectTimeout(10)->timeout(30)->withHeaders($request->getHeaders())
                    ->withOptions(['allow_redirects' => false])
                    ->send($request->getMethod(), (string) $request->getUri(), ['body' => (string) $request->getBody()]);
                if (! $response->successful()) {
                    throw new GoogleDriveException($this->authenticationError($response));
                }

                return $response->toPsrResponse();
            });
            if (empty($token['access_token'])) {
                throw new \RuntimeException('Missing access token.');
            }
        } catch (GoogleDriveException $e) {
            throw $e;
        } catch (ConnectionException $e) {
            if (preg_match('/cURL error (?:60|77)\b/', $e->getMessage())) {
                throw new GoogleDriveException('The hosting server could not verify Google\'s SSL certificate. Ask your hosting provider to repair the PHP cURL CA certificate bundle.');
            }
            throw new GoogleDriveException('The hosting server could not reach Google authentication. Ask your hosting provider to check outbound HTTPS access to oauth2.googleapis.com and DNS resolution.');
        } catch (\Throwable $e) {
            // Do not expose provider responses, signed assertions or credentials in errors/logs.
            throw new GoogleDriveException('Google Drive authentication failed. Check the service-account JSON key and that PHP OpenSSL is enabled on the hosting server.');
        }
        Cache::put($key, $token['access_token'], max(1, (int) ($token['expires_in'] ?? 3600) - 60));

        return $token['access_token'];
    }

    private function authenticationError(Response $response): string
    {
        // Inspect known error codes only; never return Google's raw response or signed request.
        $error = $response->json('error');
        $description = $response->json('error_description');
        $description = is_string($description) ? strtolower($description) : '';
        if ($error === 'invalid_grant' && (str_contains($description, 'reasonable timeframe')
            || str_contains($description, 'iat and exp'))) {
            return 'Google rejected the authentication timestamp. Ask your hosting provider to synchronize the server clock.';
        }
        if ($error === 'invalid_grant') {
            return 'Google rejected the service-account credentials. Check that the uploaded JSON key is active, belongs to the service account, and the hosting server clock is correct.';
        }
        if (in_array($error, ['invalid_client', 'disabled_client', 'unauthorized_client'], true)) {
            return 'Google rejected the service account. Check that the account is enabled and the uploaded JSON key belongs to it.';
        }
        if ($response->status() === 429 || $response->serverError()) {
            return 'Google authentication is temporarily unavailable. Please wait a moment and try again.';
        }

        return 'Google Drive authentication failed. Check the service-account JSON key and the Google Cloud service-account settings.';
    }
}

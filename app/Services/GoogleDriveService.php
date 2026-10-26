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
        try {
            $auth = new ServiceAccountCredentials('https://www.googleapis.com/auth/drive', $credentials);
            $token = $auth->fetchAuthToken(function (RequestInterface $request) {
                return Http::connectTimeout(10)->timeout(30)->withHeaders($request->getHeaders())
                    ->send($request->getMethod(), (string) $request->getUri(), ['body' => (string) $request->getBody()])
                    ->toPsrResponse();
            });
            if (empty($token['access_token'])) {
                throw new \RuntimeException('Missing access token.');
            }
        } catch (\Throwable $e) {
            // Do not expose provider responses, signed assertions or credentials in errors/logs.
            throw new GoogleDriveException('Google Drive authentication failed. Check the service-account key and enable the Drive API.');
        }
        Cache::put($key, $token['access_token'], max(1, (int) ($token['expires_in'] ?? 3600) - 60));

        return $token['access_token'];
    }
}

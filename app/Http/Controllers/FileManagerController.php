<?php

namespace App\Http\Controllers;

use App\Exceptions\GoogleDriveException;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;

class FileManagerController extends Controller
{
    public function __construct(private GoogleDriveService $drive) {}

    public function index(Request $request)
    {
        $data = $request->validate([
            'folder' => ['nullable', 'string', 'regex:/^[A-Za-z0-9_-]+$/D'],
            'search' => ['nullable', 'string', 'max:200'],
            'page' => ['nullable', 'string', 'max:2048'],
            'picker' => ['nullable', 'boolean'],
        ]);
        $folderId = $data['folder'] ?? $this->drive->rootId();
        $search = $data['search'] ?? '';
        $configured = $this->drive->isConfigured();
        $error = null;
        $folder = null;
        $breadcrumbs = [];
        $listing = ['files' => []];
        try {
            if ($configured) {
                $folder = $this->drive->folder($folderId);
                $breadcrumbs = $this->drive->breadcrumbs($folder);
                $listing = $this->drive->listing($folderId, $search, $data['page'] ?? null, $request->boolean('picker'));
            }
        } catch (GoogleDriveException $e) {
            $error = $e->getMessage();
        }
        if ($request->boolean('picker')) {
            return response()->json([
                'folder' => $folder, 'breadcrumbs' => $breadcrumbs, 'files' => $listing['files'],
                'nextPageToken' => $listing['nextPageToken'] ?? null,
                'error' => $error ?? (! $configured ? 'Google Drive is not connected.' : null),
            ], ($error || ! $configured) ? 503 : 200);
        }

        return view('file-manager.index', compact('configured', 'error', 'folder', 'folderId', 'search', 'breadcrumbs', 'listing') + [
            'maxUploadMb' => $this->drive->maxUploadMb(),
        ]);
    }

    public function createFolder(Request $request)
    {
        $data = $request->validate(['folder' => $this->idRules(), 'name' => $this->nameRules()]);

        return $this->perform($data['folder'], function () use ($data) {
            $this->drive->createFolder($data['folder'], $data['name']);
        }, 'Folder created.');
    }

    public function upload(Request $request)
    {
        $data = $request->validate([
            'folder' => $this->idRules(),
            'file' => ['bail', 'required', 'file', 'max:'.($this->drive->maxUploadMb() * 1024), function ($attribute, $value, $fail) {
                if ($value->getSize() === 0) {
                    $fail('The file must not be empty.');
                }
            }],
        ]);

        return $this->perform($data['folder'], function () use ($data) {
            $this->drive->upload($data['folder'], $data['file']);
        }, 'File uploaded to Google Drive.');
    }

    public function update(Request $request, string $file)
    {
        $data = $request->validate([
            'folder' => $this->idRules(),
            'name' => ['required_without:destination', 'nullable', ...array_slice($this->nameRules(), 1)],
            'destination' => ['required_without:name', 'nullable', 'string', 'regex:/^[A-Za-z0-9_-]+$/D'],
        ]);

        return $this->perform($data['folder'], function () use ($file, $data) {
            $this->drive->update($file, $data['name'] ?? null, $data['destination'] ?? null);
        }, 'Changes saved to Google Drive.');
    }

    public function trash(Request $request, string $file)
    {
        $data = $request->validate(['folder' => $this->idRules()]);

        return $this->perform($data['folder'], function () use ($file) {
            $this->drive->trash($file);
        }, 'Moved to trash. You can restore it from Google Drive.');
    }

    public function download(string $file)
    {
        try {
            [$stream, $name] = $this->drive->download($file);
        } catch (GoogleDriveException $e) {
            return redirect()->route('file-manager.index')->with('drive_error', $e->getMessage());
        }
        $name = trim(preg_replace('/[\x00-\x1F\x7F\/\\\\]/u', '_', $name)) ?: 'download';

        return response()->streamDownload(function () use ($stream) {
            try {
                while (! $stream->eof()) {
                    echo $stream->read(65536);
                }
            } finally {
                $stream->close();
            }
        }, $name, ['Content-Type' => 'application/octet-stream', 'Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff']);
    }

    private function perform(string $folder, callable $action, string $message)
    {
        try {
            $action();
        } catch (GoogleDriveException $e) {
            return redirect()->route('file-manager.index', ['folder' => $folder])->with('drive_error', $e->getMessage());
        }

        return redirect()->route('file-manager.index', ['folder' => $folder])->with('success', $message);
    }

    private function idRules(): array
    {
        return ['required', 'string', 'regex:/^[A-Za-z0-9_-]+$/D'];
    }

    private function nameRules(): array
    {
        return ['required', 'string', 'max:255', 'not_regex:/[\x00-\x1F\x7F]/u'];
    }
}

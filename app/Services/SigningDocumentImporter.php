<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class SigningDocumentImporter
{
    public const MAX_BYTES = 15 * 1024 * 1024;

    public const MIME_TYPES = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.google-apps.document',
    ];

    public function __construct(private GoogleDriveService $drive) {}

    /** Download a private snapshot; the caller must remove the temporary file. */
    public function download(string $id): UploadedFile
    {
        // item() verifies that this ID belongs to Our Care's configured drive.
        $item = $this->drive->item($id);
        if (! in_array($item['mimeType'], self::MIME_TYPES, true)) {
            throw ValidationException::withMessages(['drive_files' => 'Choose PDF, DOC, DOCX, or Google Docs files.']);
        }
        if ((int) ($item['size'] ?? 0) > self::MAX_BYTES) {
            throw ValidationException::withMessages(['drive_files' => 'Each document must be 15 MB or smaller.']);
        }

        [$stream, $name] = $this->drive->download($id);
        $path = null;
        $output = null;
        try {
            $path = tempnam(sys_get_temp_dir(), 'signing-drive-');
            $output = $path === false ? false : fopen($path, 'wb');
            if ($output === false) {
                throw new RuntimeException('Unable to prepare the signing document.');
            }
            $size = 0;
            while (! $stream->eof()) {
                $chunk = $stream->read(65536);
                $size += strlen($chunk);
                if ($size > self::MAX_BYTES) {
                    throw ValidationException::withMessages(['drive_files' => 'Each document must be 15 MB or smaller.']);
                }
                if (fwrite($output, $chunk) !== strlen($chunk)) {
                    throw new RuntimeException('Unable to save the signing document.');
                }
            }
            fclose($output);
            $output = null;
            if ($size === 0) {
                throw ValidationException::withMessages(['drive_files' => 'The selected document is empty.']);
            }
            $name = trim(preg_replace('/[\x00-\x1F\x7F\/\\\\]/u', '_', $name)) ?: 'document.pdf';
            $file = new UploadedFile($path, $name, null, null, true);
            // Check downloaded content too; do not trust Drive metadata alone.
            Validator::make(['drive_files' => [$file]], [
                'drive_files.*' => ['file', 'mimes:pdf,doc,docx', 'max:15360'],
            ], ['drive_files.*.mimes' => 'Each document must contain a PDF, DOC, or DOCX file.'])->validate();

            return $file;
        } catch (Throwable $exception) {
            if (is_resource($output)) {
                fclose($output);
            }
            if (is_string($path) && is_file($path)) {
                unlink($path);
            }
            throw $exception;
        } finally {
            $stream->close();
        }
    }
}

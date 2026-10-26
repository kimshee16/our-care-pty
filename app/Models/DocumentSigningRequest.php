<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentSigningRequest extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_SENT = 'sent';
    public const STATUS_SIGNING_READY = 'signing_ready';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_VOIDED = 'voided';
    public const STATUS_DECLINED = 'declined';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'reference_code',
        'client_id',
        'created_by',
        'title',
        'message',
        'document_path',
        'document_original_name',
        'document_files',
        'docusign_template_id',
        'docusign_envelope_id',
        'docusign_recipient_id',
        'docusign_client_user_id',
        'status',
        'provider_status',
        'signing_url',
        'signing_url_expires_at',
        'sent_at',
        'signed_at',
        'voided_at',
        'last_synced_at',
        'provider_payload',
    ];

    protected $casts = [
        'document_files' => 'array',
        'provider_payload' => 'array',
        'sent_at' => 'datetime',
        'signed_at' => 'datetime',
        'voided_at' => 'datetime',
        'last_synced_at' => 'datetime',
        'signing_url_expires_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::created(function (DocumentSigningRequest $signingRequest) {
            if (! $signingRequest->reference_code) {
                $signingRequest->forceFill([
                    'reference_code' => static::makeReferenceCode($signingRequest),
                ])->saveQuietly();
            }
        });
    }

    public static function makeReferenceCode(DocumentSigningRequest $signingRequest): string
    {
        $date = optional($signingRequest->created_at)->format('Ymd') ?: now()->format('Ymd');

        return 'DSR-' . $date . '-' . str_pad((string) $signingRequest->id, 6, '0', STR_PAD_LEFT);
    }

    public function displayReference(): string
    {
        return $this->reference_code ?: static::makeReferenceCode($this);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isComplete(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function canBeSent(): bool
    {
        return in_array($this->status, [
            self::STATUS_DRAFT,
            self::STATUS_FAILED,
        ], true);
    }

    public function documentFiles(): array
    {
        $files = collect($this->document_files ?: [])
            ->filter(fn ($file) => is_array($file) && ! empty($file['path']))
            ->values()
            ->all();

        if ($files !== []) {
            return $files;
        }

        if (! $this->document_path) {
            return [];
        }

        return [[
            'path' => $this->document_path,
            'original_name' => $this->document_original_name ?: basename($this->document_path),
        ]];
    }

    public function documentDisplayNames(): array
    {
        return collect($this->documentFiles())
            ->map(fn ($file) => $file['original_name'] ?? basename($file['path']))
            ->all();
    }
}

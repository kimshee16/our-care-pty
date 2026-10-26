<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_signing_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('message')->nullable();
            $table->string('document_path')->nullable();
            $table->string('document_original_name')->nullable();
            $table->json('document_files')->nullable();
            $table->string('docusign_template_id')->nullable();
            $table->string('docusign_envelope_id')->nullable()->unique();
            $table->string('docusign_recipient_id')->default('1');
            $table->string('docusign_client_user_id')->nullable();
            $table->string('status')->default('draft');
            $table->string('provider_status')->nullable();
            $table->text('signing_url')->nullable();
            $table->timestamp('signing_url_expires_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->timestamp('voided_at')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->json('provider_payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_signing_requests');
    }
};

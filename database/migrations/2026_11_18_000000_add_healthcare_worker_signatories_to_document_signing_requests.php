<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_signing_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->nullable()->change();
            $table->foreignId('healthcare_worker_user_id')->nullable()->constrained('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        if (DB::table('document_signing_requests')->whereNull('client_id')->exists()) {
            throw new RuntimeException('Rolling back would invalidate healthcare worker signing requests.');
        }

        Schema::table('document_signing_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('healthcare_worker_user_id');
            $table->unsignedBigInteger('client_id')->nullable(false)->change();
        });
    }
};

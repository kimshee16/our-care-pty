<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_signing_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('document_signing_requests', 'document_files')) {
                $table->json('document_files')->nullable()->after('document_original_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('document_signing_requests', function (Blueprint $table) {
            if (Schema::hasColumn('document_signing_requests', 'document_files')) {
                $table->dropColumn('document_files');
            }
        });
    }
};

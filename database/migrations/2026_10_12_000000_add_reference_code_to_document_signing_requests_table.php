<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_signing_requests', function (Blueprint $table) {
            $table->string('reference_code')->nullable()->unique()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('document_signing_requests', function (Blueprint $table) {
            $table->dropUnique('document_signing_requests_reference_code_unique');
            $table->dropColumn('reference_code');
        });
    }
};

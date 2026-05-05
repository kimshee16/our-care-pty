<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            if (!Schema::hasColumn('applications', 'interview_status')) {
                $table->string('interview_status')->default('pending')->after('attachments');
            }
            if (!Schema::hasColumn('applications', 'interview_date')) {
                $table->dateTime('interview_date')->nullable()->after('interview_status');
            }
            if (!Schema::hasColumn('applications', 'interview_location')) {
                $table->string('interview_location')->nullable()->after('interview_date');
            }
            if (!Schema::hasColumn('applications', 'interview_notes')) {
                $table->text('interview_notes')->nullable()->after('interview_location');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['interview_status', 'interview_date', 'interview_location', 'interview_notes']);
        });
    }
};

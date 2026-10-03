<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('healthcare_workers') && !Schema::hasColumn('healthcare_workers', 'resume_path')) {
            Schema::table('healthcare_workers', function (Blueprint $table) {
                $column = $table->string('resume_path')->nullable();

                if (Schema::hasColumn('healthcare_workers', 'profile_photo')) {
                    $column->after('profile_photo');
                } else {
                    $column->after('credentials');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('healthcare_workers') && Schema::hasColumn('healthcare_workers', 'resume_path')) {
            Schema::table('healthcare_workers', function (Blueprint $table) {
                $table->dropColumn('resume_path');
            });
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('healthcare_workers') && !Schema::hasColumn('healthcare_workers', 'profile_photo')) {
            Schema::table('healthcare_workers', function (Blueprint $table) {
                $table->string('profile_photo')->nullable()->after('credentials');
            });
        }

        if (Schema::hasTable('workers') && !Schema::hasColumn('workers', 'profile_photo')) {
            Schema::table('workers', function (Blueprint $table) {
                $table->string('profile_photo')->nullable()->after('credentials');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('healthcare_workers') && Schema::hasColumn('healthcare_workers', 'profile_photo')) {
            Schema::table('healthcare_workers', function (Blueprint $table) {
                $table->dropColumn('profile_photo');
            });
        }

        if (Schema::hasTable('workers') && Schema::hasColumn('workers', 'profile_photo')) {
            Schema::table('workers', function (Blueprint $table) {
                $table->dropColumn('profile_photo');
            });
        }
    }
};

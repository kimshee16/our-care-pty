<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('healthcare_workers', function (Blueprint $table) {
            if (!Schema::hasColumn('healthcare_workers', 'location')) {
                $table->string('location')->nullable()->after('facility_address');
            }
        });

        DB::table('healthcare_workers')
            ->select('id', 'city', 'state')
            ->orderBy('id')
            ->get()
            ->each(function ($worker) {
                $city = trim((string) ($worker->city ?? ''));
                $state = trim((string) ($worker->state ?? ''));
                $location = trim($city . ($city !== '' && $state !== '' ? ', ' : '') . $state);

                if ($location !== '') {
                    DB::table('healthcare_workers')
                        ->where('id', $worker->id)
                        ->update(['location' => $location]);
                }
            });

        Schema::table('healthcare_workers', function (Blueprint $table) {
            if (Schema::hasColumn('healthcare_workers', 'city')) {
                $table->dropColumn('city');
            }

            if (Schema::hasColumn('healthcare_workers', 'state')) {
                $table->dropColumn('state');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('healthcare_workers', function (Blueprint $table) {
            if (!Schema::hasColumn('healthcare_workers', 'city')) {
                $table->string('city')->nullable()->after('facility_address');
            }

            if (!Schema::hasColumn('healthcare_workers', 'state')) {
                $table->string('state')->nullable()->after('city');
            }
        });

        DB::table('healthcare_workers')
            ->select('id', 'location')
            ->orderBy('id')
            ->get()
            ->each(function ($worker) {
                $location = trim((string) ($worker->location ?? ''));
                if ($location === '') {
                    return;
                }

                $parts = array_map('trim', explode(',', $location));
                $city = $parts[0] ?? null;
                $state = $parts[1] ?? null;

                DB::table('healthcare_workers')
                    ->where('id', $worker->id)
                    ->update([
                        'city' => $city !== '' ? $city : null,
                        'state' => $state !== '' ? $state : null,
                    ]);
            });

        Schema::table('healthcare_workers', function (Blueprint $table) {
            if (Schema::hasColumn('healthcare_workers', 'location')) {
                $table->dropColumn('location');
            }
        });
    }
};

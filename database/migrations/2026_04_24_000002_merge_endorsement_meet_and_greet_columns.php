<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('endorsements', 'meet_and_greet_date')) {
            Schema::table('endorsements', function (Blueprint $table) {
                $table->timestamp('meet_and_greet_date')->nullable()->after('client_id');
            });
        }

        if (Schema::hasColumn('endorsements', 'meet_and_greet_start')) {
            DB::table('endorsements')
                ->whereNull('meet_and_greet_date')
                ->update([
                    'meet_and_greet_date' => DB::raw('meet_and_greet_start'),
                ]);
        }

        Schema::table('endorsements', function (Blueprint $table) {
            if (Schema::hasColumn('endorsements', 'meet_and_greet_end')) {
                $table->dropColumn('meet_and_greet_end');
            }

            if (Schema::hasColumn('endorsements', 'meet_and_greet_start')) {
                $table->dropColumn('meet_and_greet_start');
            }
        });
    }

    public function down()
    {
        Schema::table('endorsements', function (Blueprint $table) {
            if (!Schema::hasColumn('endorsements', 'meet_and_greet_start')) {
                $table->timestamp('meet_and_greet_start')->nullable()->after('client_id');
            }

            if (!Schema::hasColumn('endorsements', 'meet_and_greet_end')) {
                $table->timestamp('meet_and_greet_end')->nullable()->after('meet_and_greet_start');
            }
        });

        if (Schema::hasColumn('endorsements', 'meet_and_greet_date')) {
            DB::table('endorsements')
                ->update([
                    'meet_and_greet_start' => DB::raw('meet_and_greet_date'),
                ]);
        }

        Schema::table('endorsements', function (Blueprint $table) {
            if (Schema::hasColumn('endorsements', 'meet_and_greet_date')) {
                $table->dropColumn('meet_and_greet_date');
            }
        });
    }
};

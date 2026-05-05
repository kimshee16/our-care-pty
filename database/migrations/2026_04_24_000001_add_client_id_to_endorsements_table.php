<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('endorsements', function (Blueprint $table) {
            if (!Schema::hasColumn('endorsements', 'client_id')) {
                $table->unsignedBigInteger('client_id')->nullable()->after('job_post_id');
                $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
            }
        });
    }

    public function down()
    {
        Schema::table('endorsements', function (Blueprint $table) {
            if (Schema::hasColumn('endorsements', 'client_id')) {
                $table->dropForeign(['client_id']);
                $table->dropColumn('client_id');
            }
        });
    }
};

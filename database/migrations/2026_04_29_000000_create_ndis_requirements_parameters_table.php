<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ndis_requirements_parameters', function (Blueprint $table) {
            $table->id();
            $table->string('requirements');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ndis_requirements_parameters');
    }
};

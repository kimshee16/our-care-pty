<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ndis_requirements_completed', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worker_id')->constrained('healthcare_workers')->onDelete('cascade');
            $table->foreignId('parameter_id')->constrained('ndis_requirements_parameters')->onDelete('cascade');
            $table->string('document_link');

            $table->unique(['worker_id', 'parameter_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('ndis_requirements_completed');
    }
};

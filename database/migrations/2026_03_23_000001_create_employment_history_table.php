<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('employment_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workers_id')->constrained('healthcare_workers')->onDelete('cascade');
            $table->string('company_name');
            $table->string('job_position');
            $table->text('summary')->nullable();
            $table->year('year_started');
            $table->year('year_ended')->nullable();
            $table->boolean('is_currently_employed')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employment_history');
    }
};

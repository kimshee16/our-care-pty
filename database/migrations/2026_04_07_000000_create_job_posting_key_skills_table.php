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
        Schema::create('job_posting_key_skills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_posting_id');
            $table->string('skill', 100);
            $table->timestamps();

            $table->foreign('job_posting_id')
                ->references('id')
                ->on('job_posting')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_posting_key_skills');
    }
};

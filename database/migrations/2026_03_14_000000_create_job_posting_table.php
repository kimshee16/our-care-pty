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
        Schema::create('job_posting', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->decimal('minimum_pay_offer', 10, 2)->nullable();
            $table->decimal('maximum_pay_offer', 10, 2)->nullable();
            $table->unsignedBigInteger('client_id');
            $table->string('location')->nullable();
            $table->string('employment_type')->nullable();
            $table->string('experience')->nullable();
            $table->string('specialty')->nullable();
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_posting');
    }
};

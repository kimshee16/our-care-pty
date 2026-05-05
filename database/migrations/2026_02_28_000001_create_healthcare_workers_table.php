<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('healthcare_workers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('profession')->nullable();
            $table->string('specialization')->nullable();
            $table->string('license_number')->nullable();
            $table->integer('experience_years')->nullable();
            $table->string('facility_name')->nullable();
            $table->string('facility_address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->text('credentials')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('healthcare_workers');
    }
};

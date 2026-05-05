<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('endorsements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('worker_id');
            $table->unsignedBigInteger('job_post_id');
            $table->timestamp('meet_and_greet_date')->nullable();
            $table->string('meet_and_greet_link')->nullable();
            $table->unsignedBigInteger('endorsed_by');
            $table->timestamps();

            $table->foreign('worker_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('job_post_id')->references('id')->on('job_posting')->onDelete('cascade');
            $table->foreign('endorsed_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('endorsements');
    }
};

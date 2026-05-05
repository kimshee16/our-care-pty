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
        if (Schema::hasTable('applications')) {
            return;
        }

        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_posting_id');
            $table->unsignedBigInteger('user_applied_id');
            $table->text('application_details')->nullable();
            $table->string('expected_salary')->nullable();
            $table->text('attachments')->nullable();
            $table->timestamps();

            $table->foreign('job_posting_id')->references('id')->on('job_postings')->onDelete('cascade');
            $table->foreign('user_applied_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['job_posting_id', 'user_applied_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('applications');
    }
};

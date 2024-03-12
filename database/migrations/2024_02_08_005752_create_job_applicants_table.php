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
        Schema::create('job_applicants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('manpowerrequests_id');
            $table->foreign('manpowerrequests_id')->references('id')->on('manpower_requests');
            $table->string("application_name");
            $table->string("application_letter_attachment");
            $table->string("resume_attachment");
            $table->enum('status', ['Pending','Interviewed','Rejected','Hired']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applicants');
    }
};

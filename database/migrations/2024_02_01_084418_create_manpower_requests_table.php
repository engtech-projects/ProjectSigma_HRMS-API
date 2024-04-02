<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('manpower_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requesting_department');
            $table->foreign('requesting_department')->references('id')->on('departments');
            $table->date('date_requested');
            $table->date('date_required');
            $table->string("position");
            $table->enum('employment_type', ['Student Trainee','Project Hire','Contractual','Regular']);
            $table->text("brief_description");
            $table->string("job_description_attachment");
            $table->enum('nature_of_request', ['New/Addition','Replacement']);
            $table->string("age_range");
            $table->enum('status', ['Single','Married','No Preference']);
            $table->enum('gender', ['Male','Female', 'No preference']);
            $table->string("educational_requirement");
            $table->text("preferred_qualifications");
            $table->json('approvals');
            $table->text("remarks");
            $table->enum('request_status', ['Pending','Approved','Filled','Hold','Cancelled','Disapproved']);
            $table->integer("charged_to");
            $table->string("breakdown_details");
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manpower_requests');
    }
};

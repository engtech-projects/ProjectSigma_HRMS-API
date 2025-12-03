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
        Schema::dropIfExists('pan_work_assignment');
        Schema::dropIfExists('employee_internalwork_assignment');
        Schema::create('internal_work_experience_projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('internal_work_experience_id');
            $table->unsignedBigInteger('project_id');
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('employee_pan_request_projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_pan_request_id');
            $table->unsignedBigInteger('project_id');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internal_work_experience_projects');
        Schema::dropIfExists('employee_pan_request_projects');
        Schema::create('employee_internalwork_assignment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internal_work_experience_id');
            $table->foreign('internal_work_experience_id')->references('id')->on('internal_work_experiences')->name('internal_work_experiences_fk');
            $table->morphs('work_assignment', 'work_assignment');
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('pan_work_assignment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_pan_request_id');
            $table->foreign('employee_pan_request_id')->references('id')->on('employee_pan_requests')->name('employee_pan_requests_fk');
            $table->morphs('work_assignment', 'work_assignment');
            $table->softDeletes();
            $table->timestamps();
        });
    }
};

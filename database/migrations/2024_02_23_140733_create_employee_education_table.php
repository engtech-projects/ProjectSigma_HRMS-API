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
        Schema::create('employee_education', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->string("elementary_name");
            $table->string("elementary_education");
            $table->string("elementary_period_attendance_to");
            $table->string("elementary_period_attendance_from");
            $table->string("elementary_year_graduated");
            $table->string("secondary_name");
            $table->string("secondary_education");
            $table->string("secondary_period_attendance_to");
            $table->string("secondary_period_attendance_from");
            $table->string("secondary_year_graduated");
            $table->string("vocationalcourse_name");
            $table->string("vocationalcourse_education");
            $table->string("vocationalcourse_period_attendance_to");
            $table->string("vocationalcourse_period_attendance_from");
            $table->string("vocationalcourse_year_graduated");
            $table->string("college_name");
            $table->string("college_education");
            $table->string("college_period_attendance_to");
            $table->string("college_period_attendance_from");
            $table->string("college_year_graduated");
            $table->string("graduatestudies_name");
            $table->string("graduatestudies_education");
            $table->string("graduatestudies_period_attendance_to");
            $table->string("graduatestudies_period_attendance_from");
            $table->string("graduatestudies_year_graduated");
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_education');
    }
};

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
        Schema::create('employee_personnel_action_notice_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->enum('type', ['New Hire', 'Termination', 'Transfer', 'Promotion']);
            $table->date("date_of_effictivity");
            $table->string("section_department");
            $table->string("designation_position");
            $table->string("salary_grade");
            $table->string("salary_grade_step");
            $table->enum("salary_type", ["Fixed Rate", "Non Fixed", "Monthly", "Weekly"]);
            $table->enum("hire_source", ["Internal", "External"]);
            $table->string("work_location");
            $table->string("new_section");
            $table->string("new_location");
            $table->string("new_employment_status");
            $table->string("new_position");
            $table->string("new_salary_grade");
            $table->string("new_salary_grade Step");
            $table->string("type_of_termination");
            $table->string("reasons_for_termination");
            $table->string("eligible_for_rehire");
            $table->string("last_day_worked");
            $table->string("approvals");
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_personnel_action_notice_requests');
    }
};

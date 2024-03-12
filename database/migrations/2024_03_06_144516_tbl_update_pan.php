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
        Schema::table('employee_personnel_action_notice_requests', function (Blueprint $table) {
            $table->dropColumn('salary_grade');
            $table->dropColumn('salary_grade_step');
            $table->dropColumn('salary_type');
            $table->dropColumn('new_salary_grade');
            $table->dropColumn('new_salary_grade_step');
            $table->unsignedBigInteger('employee_id')->nullable()->change();
            $table->unsignedBigInteger('new_salary_grades')->nullable();
            $table->unsignedBigInteger('pan_job_applicant_id')->nullable();
            $table->unsignedBigInteger('salary_grades');
            $table->foreign('new_salary_grades', 'new_salary_grades')->references('id')->on('salary_grade_steps');
            $table->foreign('salary_grades')->references('id')->on('salary_grade_steps');
            $table->foreign('pan_job_applicant_id', 'pan_job_applicant_id')->references('id')->on('job_applicants');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_personnel_action_notice_requests', function (Blueprint $table) {
            $table->dropForeign('pan_job_applicant_id');
            $table->dropForeign(['salary_grades']);
            $table->dropForeign('new_salary_grades');
            $table->dropColumn('pan_job_applicant_id');
            $table->dropColumn('salary_grades');
            $table->dropColumn('new_salary_grades');
            $table->string('salary_grade');
            $table->string('salary_grade_step');
            $table->enum("salary_type", ["Fixed Rate","Non Fixed","Monthly","Weekly"]);
            $table->string('new_salary_grade_step');
            $table->string('new_salary_grade');
        });
    }
};

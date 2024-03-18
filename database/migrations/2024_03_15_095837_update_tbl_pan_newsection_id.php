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
            $table->dropColumn('new_section');
            $table->unsignedBigInteger('new_section_id')->nullable();
            $table->foreign('new_section_id', 'new_section_id')->references('id')->on('departments');
        });
        Schema::table('internal_work_experiences', function (Blueprint $table) {
            $table->dropColumn('department');
            $table->unsignedBigInteger('department_id')->nullable();
            $table->foreign('department_id', 'department_id')->references('id')->on('departments');
        });
        Schema::table('job_applicants', function (Blueprint $table) {
            $table->string('remarks')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_personnel_action_notice_requests', function (Blueprint $table) {
            $table->string('new_section');
            $table->dropForeign('new_section_id');
            $table->dropColumn('new_section_id');
        });
        Schema::table('internal_work_experiences', function (Blueprint $table) {
            $table->string('department');
            $table->dropForeign('department_id');
            $table->dropColumn('department_id');
        });
        Schema::table('job_applicants', function (Blueprint $table) {
            $table->dropColumn('remarks');
        });
    }
};

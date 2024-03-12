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
            $table->dropColumn('new_salary_grade Step');
            $table->string('new_salary_grade_step');
            $table->json('approvals')->change();
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_personnel_action_notice_requests', function (Blueprint $table) {
            $table->dropColumn('approvals');
            $table->dropColumn('new_salary_grade_step');
            $table->string('new_salary_grade Step');
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });
    }
};

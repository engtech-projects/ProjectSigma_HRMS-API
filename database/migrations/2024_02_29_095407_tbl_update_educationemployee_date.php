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
        Schema::table('employees', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable()->change();
        });
        Schema::table('employee_affiliations', function (Blueprint $table) {
            $table->date('membership_exp_date')->nullable()->change();
        });
        Schema::table('employee_eligibilities', function (Blueprint $table) {
            $table->date('cert_exp_date')->nullable()->change();
        });
        Schema::table('employee_personnel_action_notice_requests', function (Blueprint $table) {
            $table->date('date_of_effictivity')->nullable()->change();
        });
        Schema::table('employee_relatedpeople', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable()->change();
        });
        Schema::table('employee_seminartrainings', function (Blueprint $table) {
            $table->date('inclusive_dates')->nullable()->change();
        });
        Schema::table('employment_records', function (Blueprint $table) {
            $table->date('date_to')->nullable()->change();
            $table->date('date_from')->nullable()->change();
        });
        Schema::table('company_employments', function (Blueprint $table) {
            $table->date('date_hired')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->date('date_of_birth')->change();
        });
        Schema::table('employee_affiliations', function (Blueprint $table) {
            $table->date('membership_exp_date')->change();
        });
        Schema::table('employee_eligibilities', function (Blueprint $table) {
            $table->date('cert_exp_date')->change();
        });
        Schema::table('employee_personnel_action_notice_requests', function (Blueprint $table) {
            $table->date('date_of_effictivity')->change();
        });
        Schema::table('employee_relatedpeople', function (Blueprint $table) {
            $table->date('date_of_birth')->change();
        });
        Schema::table('employee_seminartrainings', function (Blueprint $table) {
            $table->date('inclusive_dates')->change();
        });
        Schema::table('employment_records', function (Blueprint $table) {
            $table->date('date_to')->change();
            $table->date('date_from')->change();
        });
        Schema::table('company_employments', function (Blueprint $table) {
            $table->date('date_hired')->change();
        });
    }
};

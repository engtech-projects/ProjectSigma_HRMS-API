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
        Schema::table('payroll_details', function (Blueprint $table) {
            $table->double('sss_employee_contribution')->nullable()->change();
            $table->double('sss_employer_contribution')->nullable()->change();
            $table->double('sss_employee_compensation')->nullable()->change();
            $table->double('sss_employer_compensation')->nullable()->change();
            $table->double('philhealth_employee_contribution')->nullable()->change();
            $table->double('philhealth_employer_contribution')->nullable()->change();
            $table->double('pagibig_employee_contribution')->nullable()->change();
            $table->double('pagibig_employer_contribution')->nullable()->change();
            $table->double('pagibig_employee_compensation')->nullable()->change();
            $table->double('pagibig_employer_compensation')->nullable()->change();
            $table->double('withholdingtax_contribution')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_details', function (Blueprint $table) {
            $table->double('sss_employee_contribution')->nullable()->change();
            $table->double('sss_employer_contribution')->nullable()->change();
            $table->double('sss_employee_compensation')->nullable()->change();
            $table->double('sss_employer_compensation')->nullable()->change();
            $table->double('philhealth_employee_contribution')->nullable()->change();
            $table->double('philhealth_employer_contribution')->nullable()->change();
            $table->double('pagibig_employee_contribution')->nullable()->change();
            $table->double('pagibig_employer_contribution')->nullable()->change();
            $table->double('pagibig_employee_compensation')->nullable()->change();
            $table->double('pagibig_employer_compensation')->nullable()->change();
            $table->double('withholdingtax_contribution')->nullable()->change();
        });
    }
};

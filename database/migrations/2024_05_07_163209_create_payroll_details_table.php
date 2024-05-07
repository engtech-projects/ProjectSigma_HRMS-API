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
        Schema::create('payroll_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payroll_record_id');
            $table->unsignedBigInteger('employee_id');
            $table->double('regular_hours');
            $table->double('rest_hours');
            $table->double('regular_holiday_hours');
            $table->double('special_holiday_hours');
            $table->double('regular_overtime');
            $table->double('rest_overtime');
            $table->double('regular_holiday_overtime');
            $table->double('special_holiday_overtime');
            $table->double('regular_pay');
            $table->double('rest_pay');
            $table->double('regular_holiday_pay');
            $table->double('special_holiday_pay');
            $table->double('regular_ot_pay');
            $table->double('rest_ot_pay');
            $table->double('regular_holiday_ot_pay');
            $table->double('special_holiday_ot_pay');
            $table->double('gross_pay');
            $table->double('late_hours');
            $table->double('sss_deduct');
            $table->double('philhealth_deduct');
            $table->double('pagibig_deduct');
            $table->double('withholdingtax_deduct');
            $table->double('total_deduct');
            $table->double('net_pay');
            $table->foreign('payroll_record_id')->references('id')->on('payroll_records');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_details');
    }
};

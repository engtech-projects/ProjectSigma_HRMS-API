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
        Schema::create('payroll_details_deductions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payroll_details_id');
            $table->enum('type', ['Cash Advance', 'Loan', 'Other Deduction', 'Others']);
            $table->morphs('deduction');
            $table->foreign('payroll_details_id')->references('id')->on('payroll_details');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_details_deductions');
    }
};

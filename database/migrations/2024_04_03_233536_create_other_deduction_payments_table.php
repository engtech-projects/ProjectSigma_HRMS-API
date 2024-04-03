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
        Schema::create('other_deduction_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('otherdeduction_id');
            $table->foreign('otherdeduction_id')->references('id')->on('other_deductions');
            $table->double('amount_paid');
            $table->date('date_paid');
            $table->enum('posting_status', ["Posted", "Not Posted"]);
            $table->enum('payment_type', ["Manual", "Payroll"]);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('other_deduction_payments');
    }
};

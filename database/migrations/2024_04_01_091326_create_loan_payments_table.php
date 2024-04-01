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
        Schema::create('loan_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId("loans_id");
            $table->foreign('loans_id')->references('id')->on('loans');
            $table->double("amount_paid");
            $table->date("date_paid");
            $table->enum("payment_type", ["Manual", "Payroll"]);
            $table->enum("posting_status", ["Posted", "Not Posted"]);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_payments');
    }
};

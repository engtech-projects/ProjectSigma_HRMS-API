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
        Schema::create('other_deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->string('otherdeduction_name');
            $table->double('total_amount');
            $table->enum('terms_of_payment', ["weekly", "monthly", "bimonthly"]);
            $table->integer('no_of_installments');
            $table->double('installment_amount');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('other_deductions');
    }
};

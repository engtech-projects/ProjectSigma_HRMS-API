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
        Schema::create('payroll_details_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->double("amount");
            $table->unsignedBigInteger('payroll_details_id');
            $table->foreign('payroll_details_id')->references('id')->on('payroll_details');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_details_adjustments');
    }
};

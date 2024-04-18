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
        Schema::create('overtime_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('overtime_id');
            $table->foreign('overtime_id')->references('id')->on('overtime');
            $table->foreignId('employee_id');
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
        Schema::dropIfExists('overtime_employees');
    }
};

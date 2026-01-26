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
        Schema::create('request_salary_disbursement_payroll_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("request_salary_disbursement_id");
            $table->unsignedInteger("payroll_record_id");
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_salary_disbursement_payroll_records');
    }
};

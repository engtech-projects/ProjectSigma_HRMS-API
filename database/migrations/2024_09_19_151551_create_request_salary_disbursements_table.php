<?php

use App\Enums\DisbursementStatus;
use App\Enums\PayrollType;
use App\Enums\ReleaseType;
use App\Enums\RequestStatuses;
use App\Enums\RequestStatusType;
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
        Schema::create('request_salary_disbursements', function (Blueprint $table) {
            $table->id();
            $table->date("payroll_date");
            $table->enum("payroll_type", PayrollType::toArray());
            $table->enum("release_type", ReleaseType::toArray());
            $table->json('approvals');
            $table->enum('request_status', RequestStatuses::toArray());
            $table->enum('disbursement_status', DisbursementStatus::toArray());
            $table->unsignedBigInteger('created_by');
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_salary_disbursements');
    }
};

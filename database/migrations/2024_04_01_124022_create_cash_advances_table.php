<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cash_advances', function (Blueprint $table) {
            $table->id();
            $table->foreignId("employee_id");
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreignId("department_id");
            $table->foreign('department_id')->references('id')->on('departments');
            $table->foreignId("project_id");
            $table->foreign('project_id')->references('id')->on('projects');
            $table->double("amount_requested");
            $table->double("amount_approved");
            $table->string("purpose");
            $table->string("terms_of_cash_advance");
            $table->string("remarks")->nullable();
            $table->json("approvals");
            $table->enum("request_status", ["Pending", "Approved", "Denied", "Released"]);
            $table->foreignId("released_by");
            $table->foreign('released_by')->references('id')->on('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_advances');
    }
};

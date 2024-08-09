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
        Schema::create('overtime', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreignId('project_id');
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreignId('department_id');
            $table->foreign('department_id')->references('id')->on('departments');
            $table->date("overtime_date");
            $table->time("overtime_start_time");
            $table->time("overtime_end_time");
            $table->string("reason");
            $table->foreignId('prepared_by');
            $table->foreign('prepared_by')->references('id')->on('users');
            $table->json("approvals");
            $table->enum("request_status", ["Pending", "Approved", "Denied", "Released"]);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('overtime');
    }
};

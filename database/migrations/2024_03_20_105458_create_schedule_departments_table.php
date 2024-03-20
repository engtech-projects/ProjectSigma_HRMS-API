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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->enum("groupType", ["department", "project", "employee"]);
            $table->foreignId("department_id")->nullable();
            $table->foreign('department_id')->references('id')->on('departments');
            $table->integer("project_id")->nullable();
            $table->foreignId("employee_id")->nullable();
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->enum("scheduleType", ["Regular", "Irregular"]);
            $table->json("daysOfWeek");
            $table->time("startTime");
            $table->time("endTime");
            $table->date("startRecur");
            $table->date("endRecur");
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};

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
        Schema::create('employee_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId("employee_id");
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreignId("department_id");
            $table->foreign('department_id')->references('id')->on('departments');
            $table->foreignId("project_id")->nullable();
            $table->enum("type", [
                "Sick/Checkup", "Special Celebration", "Vacation", "Mandatory Leave",
                "Bereavement", "Maternity/Paternity", "Other"
            ]);
            $table->string("other_absence");
            $table->date("date_of_absence_from");
            $table->date("date_of_absence_to");
            $table->string("reason_for_absence");
            $table->json("approvals");
            $table->enum("request_status", ['Pending', 'Approved', 'Filled', 'Hold', 'Cancelled', 'Disapproved']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_leaves');
    }
};

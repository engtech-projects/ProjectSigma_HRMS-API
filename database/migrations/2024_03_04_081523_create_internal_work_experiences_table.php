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
        Schema::create('internal_work_experiences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->string("position_title");
            $table->string("employment_status");
            $table->string("department");
            $table->string("immediate_supervisor");
            $table->string("salary_grade");
            $table->string("actual_salary");
            $table->string("work_location");
            $table->string("hire_source");
            $table->enum('status', ['active','inactive']);
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internal_work_experiences');
    }
};

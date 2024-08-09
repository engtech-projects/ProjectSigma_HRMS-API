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
        Schema::create('payroll_records', function (Blueprint $table) {
            $table->id();
            $table->enum('group_type', ['Project', 'Department']);
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('department_id');
            $table->enum('payroll_type', ['Weekly', 'Monthly', 'Bi-monthly']);
            $table->date('payroll_date');
            $table->date('cutoff_start');
            $table->date('cutoff_end');
            $table->json('approvals');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_records');
    }
};

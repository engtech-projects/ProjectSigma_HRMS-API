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
        Schema::create('request_13th_month_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("request_13th_months_id");
            $table->unsignedBigInteger("employee_id");
            $table->json("metadata")->nullable();
            $table->index(['request_13th_months_id'], 'r13md_request_13th_months_id_index');
            $table->index(['employee_id'], 'r13md_employee_id_index');
            $table->index(['request_13th_months_id', 'employee_id'], 'r13md_request_13th_months_id_employee_id_index'); // Composite index for common queries
            $table->timestamps();
            $table->softDeletes();
            $table->foreign("request_13th_months_id")->references("id")->on("request_13th_months");
            $table->foreign("employee_id")->references("id")->on("employees");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_13th_month_details');
    }
};

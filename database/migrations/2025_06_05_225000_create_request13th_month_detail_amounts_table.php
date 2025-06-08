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
        Schema::create('request_13th_month_detail_amt', function (Blueprint $table) {
            $table->id();
            $table->foreignId("request_13th_month_detail_id")
                  ->constrained("request_13th_month_details")
                  ->name("r13mda_foreign_detail_id");
            $table->morphs("charge");
            $table->decimal("total_payroll", 15, 2);
            $table->decimal("amount", 15, 2);
            $table->json("metadata")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_13th_month_detail_amt');
    }
};

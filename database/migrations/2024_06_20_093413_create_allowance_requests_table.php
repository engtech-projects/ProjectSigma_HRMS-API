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
        Schema::create('allowance_request', function (Blueprint $table) {
            $table->id();
            $table->morphs('charge_assignment', 'charge_assignment');
            $table->date('allowance_date');
            $table->float('allowance_amount');
            $table->date("cutoff_start");
            $table->date("cutoff_end");
            $table->integer("total_days");
            $table->enum('request_status', ['Pending','Approved','Denied','Released']);
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
        Schema::dropIfExists('allowance_request');
    }
};

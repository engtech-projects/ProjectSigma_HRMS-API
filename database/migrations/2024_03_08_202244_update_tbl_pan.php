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
        Schema::table('employee_personnel_action_notice_requests', function (Blueprint $table) {
            $table->enum('request_status', ['Pending','Approved','Filled','Hold','Cancelled','Disapproved']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_personnel_action_notice_requests', function (Blueprint $table) {
            $table->dropColumn('request_status');
        });
    }
};

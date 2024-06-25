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
        Schema::table('employee_allowances', function (Blueprint $table) {
            $table->dropColumn('charge_assignment_id');
            $table->dropColumn('charge_assignment_type');
            $table->dropColumn('cutoff_start');
            $table->dropColumn('allowance_date');
            $table->dropColumn('cutoff_end');
            $table->dropColumn('total_days');
            $table->dropColumn('request_status');
            $table->dropColumn('approvals');
            $table->after('allowance_amount', function (Blueprint $table) {
            $table->unsignedBigInteger('allowance_request_id')->nullable();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->foreign('allowance_request_id')->references('id')->on('allowance_request');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->float('allowance_rate');
            $table->float('allowance_days');
            $table->unsignedBigInteger('created_by')->nullable()->after('allowance_days');
            $table->foreign('created_by')->references('id')->on('employees');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_allowances', function (Blueprint $table) {
            $table->morphs('charge_assignment', 'charge_assignment');
            $table->date("cutoff_start")->nullable();
            $table->date("allowance_date")->nullable();
            $table->date("cutoff_end")->nullable();
            $table->integer("total_days")->nullable();;
            $table->enum('request_status', ['Pending','Approved','Denied','Released'])->nullable();;
            $table->json('approvals')->nullable();
            $table->dropColumn('allowance_rate');
            $table->dropColumn('allowance_days');
            $table->dropForeign(['employee_id']);
            $table->dropForeign(['allowance_request_id']);
            $table->dropColumn('employee_id');
            $table->dropColumn('allowance_request_id');
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });
    }
};

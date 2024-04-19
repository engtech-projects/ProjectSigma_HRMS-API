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
        Schema::table('cash_advances', function (Blueprint $table) {
            $table->dropColumn('amount_approved');
            $table->dropColumn('amount_requested');
            $table->dropColumn('terms_of_cash_advance');
            $table->dropForeign('cash_advances_released_by_foreign');
            $table->dropColumn('released_by');
            $table->enum("terms_of_payment", ["weekly", "monthly", "bimonthly"]);
            $table->integer("no_of_installment");
            $table->double("installment_deduction");
            $table->double("amount");
            $table->date('deduction_date_start');
            $table->foreignId('project_id')->nullable()->change();
            $table->foreignId('department_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_advances', function (Blueprint $table) {
            $table->double('amount_approved');
            $table->double('amount_requested');
            $table->string('terms_of_cash_advance');
            $table->foreignId('released_by')->nullable();
            $table->foreign('released_by')->references('id')->on('users');
            $table->dropColumn("terms_of_payment");
            $table->dropColumn("no_of_installment");
            $table->dropColumn("installment_deduction");
            $table->dropColumn("amount");
            $table->dropColumn('deduction_date_start');
            $table->foreignId('project_id')->nullable()->change();
            $table->foreignId('department_id')->nullable()->change();
        });
    }
};

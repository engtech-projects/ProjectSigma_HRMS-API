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
        Schema::table('other_deductions', function (Blueprint $table) {
            $table->dropColumn('total_amount');
            $table->dropColumn('installment_amount');
            $table->double("amount");
            $table->date('deduction_date_start');
            $table->double('installment_deduction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('other_deductions', function (Blueprint $table) {
            $table->double('total_amount');
            $table->double('installment_amount');
            $table->dropColumn("amount");
            $table->dropColumn('deduction_date_start');
            $table->dropColumn('installment_deduction');
        });
    }
};

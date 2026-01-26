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
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn('loan_amount');
            $table->dropColumn('period_start');
            $table->dropColumn('period_end');
            $table->enum("terms_of_payment", ["weekly", "monthly", "bimonthly"]);
            $table->integer("no_of_installment");
            $table->double("amount");
            $table->date('deduction_date_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->double("loan_amount");
            $table->date('period_start');
            $table->date('period_end');
            $table->dropColumn("terms_of_payment");
            $table->dropColumn("no_of_installment");
            $table->dropColumn("amount");
            $table->dropColumn('deduction_date_start');
        });
    }
};

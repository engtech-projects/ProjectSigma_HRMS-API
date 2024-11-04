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
        Schema::table('payroll_details', function (Blueprint $table) {
            $table->after('sss_employer_compensation', function (Blueprint $table) {
                $table->float("sss_employee_wisp")->nullable();
                $table->float("sss_employer_wisp")->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_details', function (Blueprint $table) {
            $table->dropColumn("sss_employee_wisp");
            $table->dropColumn("sss_employer_wisp");
        });
    }
};

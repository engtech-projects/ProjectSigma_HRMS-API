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
        //
        Schema::table('pagibig_contributions', function (Blueprint $table) {
            $table->float('employee_maximum_contribution');
            $table->float('employer_maximum_contribution');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagibig_contributions', function (Blueprint $table) {
            $table->dropColumn('employee_maximum_contribution');
            $table->dropColumn('employer_maximum_contribution');
        });
    }
};

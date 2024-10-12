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
        Schema::table('sss_contributions', function (Blueprint $table) {
            $table->renameColumn('employee_contribution', 'employee_compensation');
            $table->renameColumn('employer_contribution', 'employer_compensation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sss_contributions', function (Blueprint $table) {
            $table->renameColumn('employee_compensation', 'employee_contribution');
            $table->renameColumn('employer_compensation', 'employer_contribution');
        });
    }
};

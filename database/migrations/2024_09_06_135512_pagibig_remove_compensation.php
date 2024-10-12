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
        Schema::table('pagibig_contributions', function (Blueprint $table) {
            $table->dropColumn('employee_compensation');
            $table->dropColumn('employer_compensation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagibig_contributions', function (Blueprint $table) {
            $table->float('employee_compensation');
            $table->float('employer_compensation');
        });
    }
};

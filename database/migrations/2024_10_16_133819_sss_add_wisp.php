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
        Schema::useNativeSchemaOperationsIfPossible();
        Schema::table('sss_contributions', function (Blueprint $table) {
            $table->after('employer_share', function (Blueprint $table) {
                $table->float("employee_compensation")->change();
                $table->float("employer_compensation")->change();
                $table->float("employee_wisp");
                $table->float("employer_wisp");
            });
        });
        Schema::useNativeSchemaOperationsIfPossible(false);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sss_contributions', function (Blueprint $table) {
            $table->dropColumn("employee_wisp");
            $table->dropColumn("employer_wisp");
        });
    }
};

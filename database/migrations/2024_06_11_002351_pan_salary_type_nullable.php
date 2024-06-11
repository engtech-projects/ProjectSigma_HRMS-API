<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::useNativeSchemaOperationsIfPossible();
        Schema::table('employee_pan_requests', function (Blueprint $table) {
            $table->enum("salary_type", ["Fixed Rate", "Non Fixed Rate", "Monthly", "Weekly"])->nullable()->change();
        });
        Schema::useNativeSchemaOperationsIfPossible(false);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::useNativeSchemaOperationsIfPossible();
        Schema::table('employee_pan_requests', function (Blueprint $table) {
            $table->enum("salary_type", ["Fixed Rate", "Non Fixed Rate", "Monthly", "Weekly"])->change();
        });
        Schema::useNativeSchemaOperationsIfPossible(false);
    }
};

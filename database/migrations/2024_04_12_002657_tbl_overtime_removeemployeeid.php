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

        Schema::table('overtime', function (Blueprint $table) {
            if (Schema::hasColumn('overtime', 'employee_id')) {
                $table->dropForeign('overtime_employee_id_foreign');
                $table->dropColumn('employee_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('overtime', function (Blueprint $table) {
            $table->foreignId('employee_id')->nullable();
            $table->foreign('employee_id')->references('id')->on('employees');
        });
    }
};

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
        Schema::table('salary_grade_steps', function (Blueprint $table) {
            $table->double('monthly_salary_amount')->after('step_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_grade_steps', function (Blueprint $table) {
            $table->dropColumn('monthly_salary_amount');
        });
    }
};

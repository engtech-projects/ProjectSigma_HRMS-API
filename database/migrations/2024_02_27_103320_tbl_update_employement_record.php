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
        Schema::table('employment_records', function (Blueprint $table) {
            $table->date('date_to');
            $table->date('date_from');
            $table->string('position_title');
            $table->string('company_name');
            $table->string('monthly_salary');
            $table->string('status_of_appointment');
            $table->dropColumn('employment_status');
            $table->dropColumn('position');
            $table->dropColumn('department');
            $table->dropColumn('division');
            $table->dropColumn('section_program');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employment_records', function (Blueprint $table) {
            $table->dropColumn('date_to');
            $table->dropColumn('date_from');
            $table->dropColumn('position_title');
            $table->dropColumn('company_name');
            $table->dropColumn('monthly_salary');
            $table->dropColumn('status_of_appointment');
            $table->string('employment_status');
            $table->string('position');
            $table->string('department');
            $table->string('division');
            $table->string('section_program');
        });
    }
};

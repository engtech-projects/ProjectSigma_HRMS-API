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
        Schema::table('employee_pan_requests', function (Blueprint $table) {
            $tableName = 'employee_pan_requests';
            if (Schema::hasColumn($tableName, 'new_salary_grades')) {
                $table->dropForeign(['new_salary_grades']);
                $table->dropColumn('new_salary_grades');
            }
            if (Schema::hasColumn($tableName, 'new_section_id')) {
                $table->dropForeign(['new_section_id']);
                $table->dropColumn('new_section_id');
            }
            if (Schema::hasColumn($tableName, 'new_position')) {
                $table->dropColumn('new_position');
            }
            if (Schema::hasColumn($tableName, 'new_location')) {
                $table->dropColumn('new_location');
            }
            if (Schema::hasColumn($tableName, 'new_employment_status')) {
                $table->dropColumn('new_employment_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_personnel_action_notice_requests', function (Blueprint $table) {
        });
    }
};

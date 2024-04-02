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
        Schema::table('employee_education', function (Blueprint $table) {
            $table->dropColumn('elementary_name');
            $table->dropColumn('elementary_education');
            $table->dropColumn('elementary_period_attendance_to');
            $table->dropColumn('elementary_period_attendance_from');
            $table->dropColumn('elementary_year_graduated');
            $table->dropColumn('secondary_name');
            $table->dropColumn('secondary_education');
            $table->dropColumn('secondary_period_attendance_to');
            $table->dropColumn('secondary_period_attendance_from');
            $table->dropColumn('secondary_year_graduated');
            $table->dropColumn('vocationalcourse_name');
            $table->dropColumn('vocationalcourse_education');
            $table->dropColumn('vocationalcourse_period_attendance_to');
            $table->dropColumn('vocationalcourse_period_attendance_from');
            $table->dropColumn('vocationalcourse_year_graduated');
            $table->dropColumn('college_name');
            $table->dropColumn('college_education');
            $table->dropColumn('college_period_attendance_to');
            $table->dropColumn('college_period_attendance_from');
            $table->dropColumn('college_year_graduated');
            $table->dropColumn('graduatestudies_name');
            $table->dropColumn('graduatestudies_education');
            $table->dropColumn('graduatestudies_period_attendance_to');
            $table->dropColumn('graduatestudies_period_attendance_from');
            $table->dropColumn('graduatestudies_year_graduated');
            $table->dropColumn('elementary_degree_earned_of_school');
            $table->dropColumn('elementary_honors_received');
            $table->dropColumn('secondary_degree_earned_of_school');
            $table->dropColumn('secondary_honors_received');
            $table->dropColumn('college_degree_earned_of_school');
            $table->dropColumn('college_honors_received');
            $table->dropColumn('vocationalcourse_degree_earned_of_school');
            $table->dropColumn('vocationalcourse_honors_received');
            $table->enum("type", ["elementary","secondary","vocational_course","college","graduate_studies"]);
            $table->string("name")->before('id');
            $table->string("education")->after('id');
            $table->string("period_attendance_to")->after('id');
            $table->string("period_attendance_from")->after('id');
            $table->string("year_graduated")->after('id');
            $table->string('degree_earned_of_school')->after('id');
            $table->string('honors_received')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_education', function (Blueprint $table) {
            $table->string("elementary_name");
            $table->string("elementary_education");
            $table->string("elementary_period_attendance_to");
            $table->string("elementary_period_attendance_from");
            $table->string("elementary_year_graduated");
            $table->string("secondary_name");
            $table->string("secondary_education");
            $table->string("secondary_period_attendance_to");
            $table->string("secondary_period_attendance_from");
            $table->string("secondary_year_graduated");
            $table->string("vocationalcourse_name");
            $table->string("vocationalcourse_education");
            $table->string("vocationalcourse_period_attendance_to");
            $table->string("vocationalcourse_period_attendance_from");
            $table->string("vocationalcourse_year_graduated");
            $table->string("college_name");
            $table->string("college_education");
            $table->string("college_period_attendance_to");
            $table->string("college_period_attendance_from");
            $table->string("college_year_graduated");
            $table->string("graduatestudies_name");
            $table->string("graduatestudies_education");
            $table->string("graduatestudies_period_attendance_to");
            $table->string("graduatestudies_period_attendance_from");
            $table->string("graduatestudies_year_graduated");
            $table->string('elementary_degree_earned_of_school');
            $table->string('elementary_honors_received');
            $table->string('secondary_degree_earned_of_school');
            $table->string('secondary_honors_received');
            $table->string('college_degree_earned_of_school');
            $table->string('college_honors_received');
            $table->string('vocationalcourse_degree_earned_of_school');
            $table->string('vocationalcourse_honors_received');
            $table->dropColumn("type");
            $table->dropColumn("name");
            $table->dropColumn("education");
            $table->dropColumn("period_attendance_to");
            $table->dropColumn("period_attendance_from");
            $table->dropColumn("year_graduated");
            $table->dropColumn('degree_earned_of_school');
            $table->dropColumn('honors_received');
        });
    }
};

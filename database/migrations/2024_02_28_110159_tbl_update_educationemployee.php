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
        Schema::table('employee_education', function (Blueprint $table) {
            $table->string('elementary_degree_earned_of_school');
            $table->string('elementary_honors_received');
            $table->string('secondary_degree_earned_of_school');
            $table->string('secondary_honors_received');
            $table->string('college_degree_earned_of_school');
            $table->string('college_honors_received');
            $table->string('vocationalcourse_degree_earned_of_school');
            $table->string('vocationalcourse_honors_received');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('employee_education', function (Blueprint $table) {
            $table->dropColumn('elementary_degree_earned_of_school');
            $table->dropColumn('elementary_honors_received');
            $table->dropColumn('secondary_degree_earned_of_school');
            $table->dropColumn('secondary_honors_received');
            $table->dropColumn('college_degree_earned_of_school');
            $table->dropColumn('college_honors_received');
            $table->dropColumn('vocationalcourse_degree_earned_of_school');
            $table->dropColumn('vocationalcourse_honors_received');
        });
    }
};

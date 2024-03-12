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
        Schema::table('job_applicants', function (Blueprint $table) {
            $table->dropColumn('icoe_address');
            $table->string('icoe_street');
            $table->string('icoe_brgy');
            $table->string('icoe_city');
            $table->string('icoe_zip');
            $table->string('icoe_province');
            $table->string('place_of_birth');
            $table->string('blood_type');
            $table->date('date_of_marriage')->nullable();
            $table->string('sss')->nullable();
            $table->string('philhealth')->nullable();
            $table->string('pagibig')->nullable();
            $table->string('tin')->nullable();
            $table->string('citizenship');
            $table->string('religion');
            $table->string('height');
            $table->string('weight');
            $table->string('father_name');
            $table->string('mother_name');
            $table->enum('gender', ['male', 'female']);
            $table->string('civil_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applicants', function (Blueprint $table) {
            $table->string('icoe_address');
            $table->dropColumn('icoe_street');
            $table->dropColumn('icoe_brgy');
            $table->dropColumn('icoe_city');
            $table->dropColumn('icoe_zip');
            $table->dropColumn('icoe_province');
            $table->dropColumn('place_of_birth');
            $table->dropColumn('date_of_marriage');
            $table->dropColumn('gender');
            $table->dropColumn('blood_type');
            $table->dropColumn('sss');
            $table->dropColumn('philhealth');
            $table->dropColumn('pagibig');
            $table->dropColumn('tin');
            $table->dropColumn('citizenship');
            $table->dropColumn('religion');
            $table->dropColumn('height');
            $table->dropColumn('weight');
            $table->dropColumn('father_name');
            $table->dropColumn('mother_name');
            $table->dropColumn('civil_status');
        });
    }
};

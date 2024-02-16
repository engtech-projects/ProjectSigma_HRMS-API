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
            $table->string('lastname');
            $table->string('firstname');
            $table->string('middlename');
            $table->date('date_of_application')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('address_street');
            $table->string('address_city');
            $table->string('address_zip');
            $table->bigInteger('contact_info');
            $table->string('email');
            $table->string('how_did_u_learn_about_our_company');
            $table->string('desired_position');
            $table->string('currently_employed');
            $table->string('name_of_spouse')->nullable();
            $table->date('date_of_birth_spouse')->nullable();
            $table->bigInteger('telephone_spouse')->nullable();
            $table->string('occupation_spouse')->nullable();
            $table->json('children')->nullable();
            $table->json('education')->nullable();
            $table->string('icoe_name');
            $table->string('icoe_address');
            $table->string('icoe_relationship');
            $table->bigInteger('telephone_icoe');
            $table->json('workexperience')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applicants', function (Blueprint $table) {
            $table->dropColumn('lastname');
            $table->dropColumn('firstname');
            $table->dropColumn('middlename');
            $table->dropColumn('date_of_application');
            $table->dropColumn('date_of_birth');
            $table->dropColumn('address_street');
            $table->dropColumn('address_city');
            $table->dropColumn('address_zip');
            $table->dropColumn('contact_info');
            $table->dropColumn('email');
            $table->dropColumn('how_did_u_learn_about_our_company');
            $table->dropColumn('desired_position');
            $table->dropColumn('currently_employed');
            $table->dropColumn('name_of_spouse')->nullable();
            $table->dropColumn('date_of_birth_spouse')->nullable();
            $table->dropColumn('telephone_spouse')->nullable();
            $table->dropColumn('occupation_spouse')->nullable();
            $table->dropColumn('children')->nullable();
            $table->dropColumn('education')->nullable();
            $table->dropColumn('icoe_name');
            $table->dropColumn('icoe_address');
            $table->dropColumn('icoe_relationship');
            $table->dropColumn('telephone_icoe');
            $table->dropColumn('workexperience');
        });
    }
};

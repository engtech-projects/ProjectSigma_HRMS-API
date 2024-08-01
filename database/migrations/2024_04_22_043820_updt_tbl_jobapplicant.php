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
        Schema::table('job_applicants', function (Blueprint $table) {
            $table->enum('status', ['Contact Extended', 'Pending', 'Interviewed', 'Rejected', 'Hired', 'For Hiring', 'Test,Interview', 'Reference Checking', 'Medical Examination', 'Contract Signed'])->default('Pending')->change();
            $table->string("middlename")->nullable()->change();
            $table->bigInteger("contact_info")->nullable()->change();
            $table->string("email")->nullable()->change();
            $table->string("how_did_u_learn_about_our_company")->nullable()->change();
            $table->string("desired_position")->nullable()->change();
            $table->string("name_of_spouse")->nullable()->change();
            $table->string("date_of_birth_spouse")->nullable()->change();
            $table->bigInteger("telephone_spouse")->nullable()->change();
            $table->string("occupation_spouse")->nullable()->change();
            $table->string("date_of_marriage")->nullable()->change();
            $table->string("sss")->nullable()->change();
            $table->string("philhealth")->nullable()->change();
            $table->string("pagibig")->nullable()->change();
            $table->string("tin")->nullable()->change();
            $table->string("religion")->nullable()->change();
            $table->string("father_name")->nullable()->change();
            $table->string("mother_name")->nullable()->change();
            $table->string("name_suffix")->nullable()->change();
        });
        Schema::useNativeSchemaOperationsIfPossible(false);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::useNativeSchemaOperationsIfPossible();
        Schema::table('job_applicants', function (Blueprint $table) {
            $table->enum('status', ['Contact Extended', 'Pending', 'Interviewed', 'Rejected', 'Hired', 'For Hiring', 'Test,Interview', 'Reference Checking', 'Medical Examination', 'Contract Signed'])->default('Pending')->change();
            $table->string("middlename")->nullable()->change();
            $table->bigInteger("contact_info")->nullable()->change();
            $table->string("email")->nullable()->change();
            $table->string("how_did_u_learn_about_our_company")->nullable()->change();
            $table->string("desired_position")->nullable()->change();
            $table->string("name_of_spouse")->nullable()->change();
            $table->string("date_of_birth_spouse")->nullable()->change();
            $table->bigInteger("telephone_spouse")->nullable()->change();
            $table->string("occupation_spouse")->nullable()->change();
            $table->string("date_of_marriage")->nullable()->change();
            $table->string("sss")->nullable()->change();
            $table->string("philhealth")->nullable()->change();
            $table->string("pagibig")->nullable()->change();
            $table->string("tin")->nullable()->change();
            $table->string("religion")->nullable()->change();
            $table->string("father_name")->nullable()->change();
            $table->string("mother_name")->nullable()->change();
            $table->string("name_suffix")->nullable()->change();
        });
        Schema::useNativeSchemaOperationsIfPossible(false);
    }
};

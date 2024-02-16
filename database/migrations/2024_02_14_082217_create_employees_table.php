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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string("first_name");
            $table->string("middle_name")->nullable();
            $table->string("family_name");
            $table->string("name_suffix")->nullable();
            $table->string("nick_name");
            $table->string("gender");
            $table->date("date_of_birth");
            $table->string("place_of_birth");
            $table->string("citizenship");
            $table->string("blood_type");
            $table->string("civil_status");
            $table->string("date_of_marriage");
            $table->string("telephone_number");
            $table->string("mobile_number");
            $table->string("email");
            $table->string("religion");
            $table->string("pre_street");
            $table->string("pre_brgy");
            $table->string("pre_city");
            $table->string("pre_zip");
            $table->string("pre_province");
            $table->string("per_street");
            $table->string("per_brgy");
            $table->string("per_city");
            $table->string("per_zip");
            $table->string("per_province");
            $table->string("father_name");
            $table->string("mother_name");
            $table->string("spouse_name");
            $table->string("spouse_datebirth");
            $table->string("spouse_occupation");
            $table->string("spouse_contact_no");
            $table->string("childrens");
            $table->string("person_to_contact_name");
            // $table->string("person_to_contact_address");
            $table->string("person_to_contact_street");
            $table->string("person_to_contact_brgy");
            $table->string("person_to_contact_city");
            $table->string("person_to_contact_zip");
            $table->string("person_to_province");
            $table->string("person_to_contact_no");
            $table->string("person_to_contact_relationship");
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};

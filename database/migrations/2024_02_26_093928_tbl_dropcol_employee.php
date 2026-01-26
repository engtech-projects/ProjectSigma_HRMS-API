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
        //
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn("pre_street");
            $table->dropColumn("pre_brgy");
            $table->dropColumn("pre_city");
            $table->dropColumn("pre_zip");
            $table->dropColumn("pre_province");
            $table->dropColumn("per_street");
            $table->dropColumn("per_brgy");
            $table->dropColumn("per_city");
            $table->dropColumn("per_zip");
            $table->dropColumn("per_province");
            $table->dropColumn("father_name");
            $table->dropColumn("mother_name");
            $table->dropColumn("spouse_name");
            $table->dropColumn("spouse_datebirth");
            $table->dropColumn("spouse_occupation");
            $table->dropColumn("spouse_contact_no");
            $table->dropColumn("childrens");
            $table->dropColumn("person_to_contact_name");
            $table->dropColumn("person_to_contact_street");
            $table->dropColumn("person_to_contact_brgy");
            $table->dropColumn("person_to_contact_city");
            $table->dropColumn("person_to_contact_zip");
            $table->dropColumn("person_to_province");
            $table->dropColumn("person_to_contact_no");
            $table->dropColumn("person_to_contact_relationship");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
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
            $table->string("person_to_contact_street");
            $table->string("person_to_contact_brgy");
            $table->string("person_to_contact_city");
            $table->string("person_to_contact_zip");
            $table->string("person_to_province");
            $table->string("person_to_contact_no");
            $table->string("person_to_contact_relationship");
        });
    }
};

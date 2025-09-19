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
        Schema::table('employees', function (Blueprint $table) {
            $table->string('nick_name')->nullable()->change();
            $table->string('citizenship')->nullable()->change();
            $table->string('blood_type')->nullable()->change();
            $table->string('civil_status')->nullable()->change();
            $table->date('date_of_marriage')->nullable()->change();
            $table->string('telephone_number')->nullable()->change();
            $table->string('mobile_number')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->string('religion')->nullable()->change();
            $table->string('pre_street')->nullable()->change();
            $table->string('pre_brgy')->nullable()->change();
            $table->string('pre_city')->nullable()->change();
            $table->string('pre_zip')->nullable()->change();
            $table->string('pre_province')->nullable()->change();
            $table->string('per_street')->nullable()->change();
            $table->string('per_brgy')->nullable()->change();
            $table->string('per_city')->nullable()->change();
            $table->string('per_zip')->nullable()->change();
            $table->string('per_province')->nullable()->change();
            $table->string('father_name')->nullable()->change();
            $table->string('mother_name')->nullable()->change();
            $table->string('spouse_name')->nullable()->change();
            $table->date('spouse_datebirth')->nullable()->change();
            $table->string('spouse_occupation')->nullable()->change();
            $table->string('spouse_contact_no')->nullable()->change();
            $table->string('childrens')->nullable()->change();
            $table->string('weight')->nullable();
            $table->string('height')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('nick_name')->nullable(false)->change();
            $table->string('citizenship')->nullable(false)->change();
            $table->string('blood_type')->nullable(false)->change();
            $table->string('civil_status')->nullable(false)->change();
            $table->date('date_of_marriage')->nullable(false)->change();
            $table->string('telephone_number')->nullable(false)->change();
            $table->string('mobile_number')->nullable(false)->change();
            $table->string('email')->nullable(false)->change();
            $table->string('religion')->nullable(false)->change();
            $table->string('pre_street')->nullable(false)->change();
            $table->string('pre_brgy')->nullable(false)->change();
            $table->string('pre_city')->nullable(false)->change();
            $table->string('pre_zip')->nullable(false)->change();
            $table->string('pre_province')->nullable(false)->change();
            $table->string('per_street')->nullable(false)->change();
            $table->string('per_brgy')->nullable(false)->change();
            $table->string('per_city')->nullable(false)->change();
            $table->string('per_zip')->nullable(false)->change();
            $table->string('per_province')->nullable(false)->change();
            $table->string('father_name')->nullable(false)->change();
            $table->string('mother_name')->nullable(false)->change();
            $table->string('spouse_name')->nullable(false)->change();
            $table->date('spouse_datebirth')->nullable(false)->change();
            $table->string('spouse_occupation')->nullable(false)->change();
            $table->string('spouse_contact_no')->nullable(false)->change();
            $table->string('childrens')->nullable(false)->change();
            $table->string('weight')->nullable(false)->change();
            $table->string('height')->nullable(false)->change();
            $table->dropColumn('weight');
            $table->dropColumn('height');
        });
    }
};

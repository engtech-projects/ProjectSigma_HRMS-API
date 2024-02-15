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
            $table->string("middle_name");
            $table->string("family_name");
            $table->string("name_suffix");
            $table->string("gender");
            $table->date("date_of_birth");
            $table->string("place_of_birth");
            $table->string("citizenship");
            $table->string("blood_type");
            $table->string("civil_status");
            $table->bigInteger("telephone_number");
            $table->bigInteger("mobile_number");
            $table->string("email");
            $table->string("religion");
            $table->string("curr_address");
            $table->string("perm_address");
            $table->string("father_name");
            $table->string("mother_name");
            $table->string("spouse_datebirth");
            $table->string("spouse_occupation");
            $table->bigInteger("spouse_contact_no");
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

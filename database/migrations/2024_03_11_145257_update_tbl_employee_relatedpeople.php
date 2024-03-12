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
        Schema::table('employee_relatedpeople', function (Blueprint $table) {
            $table->string("relationship")->nullable()->change();
            $table->date("date_of_birth")->nullable()->change();
            $table->string("street")->nullable()->change();
            $table->string("brgy")->nullable()->change();
            $table->string("city")->nullable()->change();
            $table->string("zip")->nullable()->change();
            $table->string("province")->nullable()->change();
            $table->string("occupation")->nullable()->change();
            $table->string("contact_no")->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_relatedpeople', function (Blueprint $table) {
            $table->string("relationship")->change();
            $table->date("date_of_birth")->change();
            $table->string("street")->change();
            $table->string("brgy")->change();
            $table->string("city")->change();
            $table->string("zip")->change();
            $table->string("province")->change();
            $table->string("occupation")->change();
            $table->string("contact_no")->change();
        });
    }
};

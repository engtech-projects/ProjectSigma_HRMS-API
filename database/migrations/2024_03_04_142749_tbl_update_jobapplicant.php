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
        //
        Schema::table('job_applicants', function (Blueprint $table) {
            $table->dropColumn('address_street');
            $table->dropColumn('address_city');
            $table->dropColumn('address_zip');
            $table->string('pre_address_street');
            $table->string('pre_address_brgy');
            $table->string('pre_address_city');
            $table->string('pre_address_zip');
            $table->string('pre_address_province');
            $table->string('per_address_street');
            $table->string('per_address_brgy');
            $table->string('per_address_city');
            $table->string('per_address_zip');
            $table->string('per_address_province');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applicants', function (Blueprint $table) {
            $table->string('address_street');
            $table->string('address_city');
            $table->string('address_zip');
            $table->dropColumn('pre_address_street');
            $table->dropColumn('pre_address_brgy');
            $table->dropColumn('pre_address_city');
            $table->dropColumn('pre_address_zip');
            $table->dropColumn('pre_address_province');
            $table->dropColumn('per_address_street');
            $table->dropColumn('per_address_brgy');
            $table->dropColumn('per_address_city');
            $table->dropColumn('per_address_zip');
            $table->dropColumn('per_address_province');
        });
    }
};

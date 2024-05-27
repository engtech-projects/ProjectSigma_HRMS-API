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
            $table->string('contact_info')->nullable()->change();
            $table->string('telephone_spouse')->nullable()->change();
            $table->string('telephone_icoe')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applicants', function (Blueprint $table) {
            $table->bigInteger('contact_info')->nullable()->change();
            $table->bigInteger('telephone_spouse')->nullable()->change();
            $table->bigInteger('telephone_icoe')->change();
        });
    }
};

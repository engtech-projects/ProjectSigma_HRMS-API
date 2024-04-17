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
        Schema::table('external_work_experiences', function (Blueprint $table) {
            $table->string("position_title")->nullable()->change();
            $table->string("company_name")->nullable()->change();
            $table->integer("salary")->nullable()->change();
            $table->string("status_of_appointment")->nullable()->change();
            $table->date("date_from")->nullable()->change();
            $table->date("date_to")->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('external_work_experiences', function (Blueprint $table) {
            $table->string("position_title")->change();
            $table->string("company_name")->change();
            $table->integer("salary")->change();
            $table->string("status_of_appointment")->change();
            $table->date("date_from")->change();
            $table->date("date_to")->change();
        });
    }
};

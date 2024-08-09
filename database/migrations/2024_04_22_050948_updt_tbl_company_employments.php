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
        Schema::table('company_employments', function (Blueprint $table) {
            $table->string("phic_number")->nullable()->change();
            $table->string("sss_number")->nullable()->change();
            $table->string("tin_number")->nullable()->change();
            $table->string("pagibig_number")->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_employments', function (Blueprint $table) {
            $table->string("phic_number")->nullable()->change();
            $table->string("sss_number")->nullable()->change();
            $table->string("tin_number")->nullable()->change();
            $table->string("pagibig_number")->nullable()->change();
        });
    }
};

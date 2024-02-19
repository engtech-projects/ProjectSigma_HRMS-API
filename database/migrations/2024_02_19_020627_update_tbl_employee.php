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
        Schema::table('employees', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable()->change();
            $table->date('spouse_datebirth')->nullable()->change();
            $table->date('date_of_marriage')->nullable()->change();
            $table->string('email')->nullable()->change();
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
            $table->date('date_of_birth')->nullable(false)->change();
            $table->date('spouse_datebirth')->nullable(false)->change();
            $table->date('date_of_marriage')->nullable(false)->change();
            $table->date('email')->nullable(false)->change();
            $table->dropColumn('weight');
            $table->dropColumn('height');
        });
    }
};

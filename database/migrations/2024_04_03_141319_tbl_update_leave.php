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
        Schema::table('employee_leaves', function (Blueprint $table) {
            $table->integer("number_of_days");
            $table->boolean("with_pay");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_leaves', function (Blueprint $table) {
            $table->dropColumn("number_of_days");
            $table->dropColumn("with_pay");
        });
    }
};

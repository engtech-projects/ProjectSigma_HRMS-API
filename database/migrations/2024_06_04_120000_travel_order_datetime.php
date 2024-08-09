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
        Schema::table('travel_orders', function (Blueprint $table) {
            $table->renameColumn("date_and_time_of_travel", "date_of_travel");
            $table->time('time_of_travel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('travel_orders', function (Blueprint $table) {
            $table->renameColumn("date_of_travel", "date_and_time_of_travel");
            $table->dropColumn('time_of_travel');
        });
    }
};

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
        Schema::useNativeSchemaOperationsIfPossible();
        Schema::table('travel_orders', function (Blueprint $table) {
            $table->nullableMorphs('charge');
        });
        Schema::useNativeSchemaOperationsIfPossible(false);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::useNativeSchemaOperationsIfPossible();
        Schema::table('travel_orders', function (Blueprint $table) {
            $table->dropMorphs('charge');
        });
        Schema::useNativeSchemaOperationsIfPossible(false);
    }
};

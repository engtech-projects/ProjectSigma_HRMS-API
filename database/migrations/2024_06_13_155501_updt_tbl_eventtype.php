<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::useNativeSchemaOperationsIfPossible();
        Schema::table('events', function (Blueprint $table) {
            $table->enum('event_type', ['Regular Holiday', 'Special Holiday', 'Company Event'])->change();
        });
        Schema::useNativeSchemaOperationsIfPossible(false);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::useNativeSchemaOperationsIfPossible();
        Schema::table('events', function (Blueprint $table) {
            $table->enum('event_type', ['Holiday','Company Event'])->change();
        });
        Schema::useNativeSchemaOperationsIfPossible(false);
    }
};

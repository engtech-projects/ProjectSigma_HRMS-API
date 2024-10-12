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
        Schema::table('failure_to_logs', function (Blueprint $table) {
            $table->nullableMorphs('charging');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('failure_to_logs', function (Blueprint $table) {
            $table->dropMorphs('charging');
        });
    }
};

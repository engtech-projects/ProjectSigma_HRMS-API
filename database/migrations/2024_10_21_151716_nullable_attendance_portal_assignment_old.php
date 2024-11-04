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
        Schema::table('attendance_portals', function (Blueprint $table) {
            $table->unsignedBigInteger('assignment_id')->nullable()->change();
            $table->string('assignment_type')->nullable()->change();
        });
        Schema::useNativeSchemaOperationsIfPossible(false);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::useNativeSchemaOperationsIfPossible();
        Schema::table('attendance_portals', function (Blueprint $table) {
            $table->unsignedBigInteger('assignment_id')->nullable(false)->change();
            $table->string('assignment_type')->nullable(false)->change();
        });
        Schema::useNativeSchemaOperationsIfPossible(false);
    }
};

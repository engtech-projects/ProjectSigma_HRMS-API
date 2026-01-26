<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::useNativeSchemaOperationsIfPossible();
        Schema::table('settings', function ($table) {
            $table->timestamp('created_at')->nullable()->after('value')->change();
            $table->timestamp('updated_at')->nullable()->after('created_at')->change();
            $table->timestamp('deleted_at')->nullable()->after('updated_at')->change();
        });
        Schema::useNativeSchemaOperationsIfPossible(false);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

<?php

use App\Enums\RequestStatuses;
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
        Schema::table('overtime', function (Blueprint $table) {
            $table->json('approvals')->change();
            $table->enum('request_status', RequestStatuses::toArray())->change();
        });
        Schema::useNativeSchemaOperationsIfPossible(false);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::useNativeSchemaOperationsIfPossible();
        Schema::table('overtime', function (Blueprint $table) {
            $table->json('approvals')->change();
            $table->enum('request_status', RequestStatuses::toArray())->change();
        });
        Schema::useNativeSchemaOperationsIfPossible(false);
    }
};

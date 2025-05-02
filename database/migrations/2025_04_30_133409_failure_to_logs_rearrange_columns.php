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
        Schema::table('failure_to_logs', function ($table) {
            $table->text('approvals')->nullable()->after('charging_id')->change();
            $table->text('request_status')->nullable()->after('approvals')->change();
            $table->timestamp('created_at')->nullable()->after('request_status')->change();
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
        Schema::table('logs_rearrange_columns', function (Blueprint $table) {
            //
        });
    }
};

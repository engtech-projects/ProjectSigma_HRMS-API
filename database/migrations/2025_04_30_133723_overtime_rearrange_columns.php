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
        Schema::table('overtime', function ($table) {
            $table->text('approvals')->nullable()->after('created_by')->change();
            $table->text('request_status')->nullable()->after('approvals')->change();
            $table->unsignedBigInteger('created_by')->nullable()->after('request_status')->change();
            $table->timestamp('created_at')->nullable()->after('created_by')->change();
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

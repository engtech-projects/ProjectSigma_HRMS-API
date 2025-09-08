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
            $table->unsignedBigInteger("created_by")
                ->after('employee_id')
                ->nullable();
            $table->foreign("created_by")
                ->references("id")
                ->on("users");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('failure_to_logs', function (Blueprint $table) {
            $table->dropForeign('failure_to_logs_created_by_foreign');
            $table->dropColumn('created_by');
        });
    }
};

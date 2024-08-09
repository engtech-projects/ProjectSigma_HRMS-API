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
        Schema::table('employee_pan_requests', function (Blueprint $table) {
            $table->string("company_id_num")->nullable()->after("employee_id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_pan_requests', function (Blueprint $table) {
            $table->dropColumn("company_id_num");
        });
    }
};

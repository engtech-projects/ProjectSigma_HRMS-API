<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('manpower_requests', function (Blueprint $table) {
            $table->dropColumn('position');
            $table->unsignedBigInteger('position_id');
            $table->foreign('position_id')->references('id')->on('positions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manpower_requests', function (Blueprint $table) {
            $table->dropForeign('manpower_requests_position_id_foreign');
            $table->dropColumn('position_id');
            $table->string('position');
        });
    }
};

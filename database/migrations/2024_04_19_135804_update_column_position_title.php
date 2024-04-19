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
        Schema::table('internal_work_experiences', function (Blueprint $table) {
            if (Schema::hasColumn('internal_work_experiences', 'position_title')) {
                $table->dropColumn('position_title');
            }
            $table->unsignedBigInteger('position_id')->nullable();
            $table->foreign('position_id')->references('id')->on('positions')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('internal_work_experiences', function (Blueprint $table) {
            Schema::disableForeignKeyConstraints('position_id');
            if (Schema::hasColumn('internal_work_experiences', 'position_id')) {
                $table->string('position_title');
                $table->dropForeign(['position_id']);
                $table->dropColumn('position_id');
            }
        });
    }
};

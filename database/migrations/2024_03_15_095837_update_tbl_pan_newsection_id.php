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
        Schema::table('employee_personnel_action_notice_requests', function (Blueprint $table) {
            $table->dropColumn('new_section');
            $table->unsignedBigInteger('new_section_id')->nullable();
            $table->foreign('new_section_id', 'new_section_id')->references('id')->on('departments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_personnel_action_notice_requests', function (Blueprint $table) {
            $table->string('new_section');
            $table->dropForeign('new_section_id');
            $table->dropColumn('new_section_id');
        });
    }
};

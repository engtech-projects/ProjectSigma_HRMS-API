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
        Schema::table('manpower_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('requested_by');
            $table->foreign('requested_by')->references('id')->on('users');
            $table->text("preferred_qualifications")->nullable()->change();
            $table->text("remarks")->nullable()->change();
            $table->integer("charged_to")->nullable()->change();
            $table->string("breakdown_details")->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('manpower_requests', function (Blueprint $table) {
            $table->dropForeign(['requested_by']);
            $table->dropColumn(['requested_by']);
            $table->text("preferred_qualifications")->change();
            $table->text("remarks")->change();
            $table->integer("charged_to")->change();
            $table->string("breakdown_details")->change();
        });
    }
};

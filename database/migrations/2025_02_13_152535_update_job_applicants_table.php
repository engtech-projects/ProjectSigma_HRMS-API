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
        Schema::table('job_applicants', function (Blueprint $table) {
            $table->string('icoe_occupation')->after('icoe_relationship');
            $table->date('icoe_date_of_birth')->nullable()->after('icoe_occupation');
            $table->string('atm')->nullable()->after('contact_info');
        });
        Schema::useNativeSchemaOperationsIfPossible(false);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applicants', function (Blueprint $table) {
            $table->dropColumn('icoe_occupation');
            $table->dropColumn('icoe_date_of_birth');
            $table->dropColumn('atm');
        });
    }
};

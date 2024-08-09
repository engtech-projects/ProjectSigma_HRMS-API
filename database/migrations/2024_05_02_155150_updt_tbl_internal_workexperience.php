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
        Schema::table('internal_work_experiences', function (Blueprint $table) {
            $table->dropColumn("employment_status");
        });

        Schema::table('internal_work_experiences', function (Blueprint $table) {
            $table->enum("employment_status", [
                "Regular", "Probationary", "Part Time", "Project Based", "Contractual"
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('internal_work_experiences', function (Blueprint $table) {
            $table->dropColumn("employment_status");
        });

        Schema::table('internal_work_experiences', function (Blueprint $table) {
            $table->string("employment_status");
        });
    }
};

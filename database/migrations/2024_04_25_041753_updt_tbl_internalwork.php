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
            $table->enum("salary_type", ["Fixed Rate", "Non Fixed Rate", "Monthly", "Weekly"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('internal_work_experiences', function (Blueprint $table) {
            $table->dropColumn("salary_type");
        });
    }
};

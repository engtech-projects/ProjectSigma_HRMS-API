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
        Schema::table('employee_leaves', function (Blueprint $table) {
            $table->dropColumn("type");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_leaves', function (Blueprint $table) {
            $table->enum("type", [
                "Sick/Checkup", "Special Celebration", "Vacation", "Mandatory Leave",
                "Bereavement", "Maternity/Paternity", "Other"
            ]);
        });
    }
};

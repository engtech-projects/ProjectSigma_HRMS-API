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
            $table->dropColumn("salary_grade");
            $table->unsignedBigInteger('salary_grades');
            $table->foreign('salary_grades')->references('id')->on('salary_grade_steps');
            $table->enum('status', ['current','previous'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('internal_work_experiences', function (Blueprint $table) {
            $table->dropForeign(['salary_grades']);
            $table->dropColumn("salary_grades");
            $table->string("salary_grade");
            $table->enum('status', ['active','inactive'])->change();
        });
    }
};

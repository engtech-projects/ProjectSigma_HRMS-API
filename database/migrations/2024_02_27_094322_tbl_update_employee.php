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
        Schema::table('employee_relatedpeople', function (Blueprint $table) {
            $table->enum('type',['contact person','dependent/children','father','mother','spouse','reference','guardian'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_relatedpeople', function (Blueprint $table) {
            $table->enum('type',['contact person','dependent/children','father','mother','spouse','reference','guardian'])->change();
        });
    }
};

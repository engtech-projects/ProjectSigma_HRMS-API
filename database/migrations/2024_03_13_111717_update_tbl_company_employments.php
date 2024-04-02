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
        Schema::table('company_employments', function (Blueprint $table) {
            $table->dropColumn('company');
            $table->dropColumn('imidiate_supervisor');
            $table->string('employeedisplay_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_employments', function (Blueprint $table) {
            $table->string('company')->nullable();
            $table->string('imidiate_supervisor')->nullable();
            $table->string('employeedisplay_id')->change();
        });
    }
};

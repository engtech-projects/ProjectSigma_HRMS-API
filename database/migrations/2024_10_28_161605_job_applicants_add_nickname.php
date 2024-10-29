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
        Schema::useNativeSchemaOperationsIfPossible();
        Schema::table('job_applicants', function (Blueprint $table) {
            $table->after('middlename', function (Blueprint $table) {
                $table->string("lastname")->change();
                $table->string("name_suffix")->nullable()->change();
                $table->string("nickname")->nullable();
            });
            $table->after('remarks', function (Blueprint $table) {
                $table->timestamp("created_at")->nullable()->change();
                $table->timestamp("updated_at")->nullable()->change();
                $table->timestamp("deleted_at")->nullable()->change();
            });
        });
        Schema::useNativeSchemaOperationsIfPossible(false);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::useNativeSchemaOperationsIfPossible();
        Schema::table('job_applicants', function (Blueprint $table) {
        });
        Schema::useNativeSchemaOperationsIfPossible(false);
    }
};

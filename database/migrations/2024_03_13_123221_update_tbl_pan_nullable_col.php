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
        Schema::table('employee_personnel_action_notice_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('salary_grades')->nullable()->change();
            $table->string('section_department')->nullable()->change();
            $table->string('designation_position')->nullable()->change();
            $table->enum('hire_source', ['Internal','External'])->nullable()->change();
            $table->string('work_location')->nullable()->change();
            $table->string('new_section')->nullable()->change();
            $table->string('new_location')->nullable()->change();
            $table->string('new_employment_status')->nullable()->change();
            $table->string('new_position')->nullable()->change();
            $table->string('type_of_termination')->nullable()->change();
            $table->string('reasons_for_termination')->nullable()->change();
            $table->string('eligible_for_rehire')->nullable()->change();
            $table->string('last_day_worked')->nullable()->change();
        });
        Schema::useNativeSchemaOperationsIfPossible(false);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::useNativeSchemaOperationsIfPossible();
        Schema::table('employee_personnel_action_notice_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('salary_grades')->change();
            $table->string('section_department')->change();
            $table->string('designation_position')->change();
            $table->enum('hire_source', ['Internal','External'])->change();
            $table->string('work_location')->change();
            $table->string('new_section')->change();
            $table->string('new_location')->change();
            $table->string('new_employment_status')->change();
            $table->string('new_position')->change();
            $table->string('type_of_termination')->change();
            $table->string('reasons_for_termination')->change();
            $table->string('eligible_for_rehire')->change();
            $table->string('last_day_worked')->change();
        });
        Schema::useNativeSchemaOperationsIfPossible(false);
    }
};

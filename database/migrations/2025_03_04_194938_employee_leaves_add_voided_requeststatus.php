<?php

use App\Enums\RequestStatuses;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::useNativeSchemaOperationsIfPossible();
        Schema::table('employee_leaves', function (Blueprint $table) {
            $table->enum('request_status', ['Approved', 'Disapproved', 'Pending', 'Void', 'Denied'])->change();
        });
        DB::table('employee_leaves')->where('request_status', 'Disapproved')->update(['request_status' => RequestStatuses::DENIED->value]);
        Schema::table('employee_leaves', function (Blueprint $table) {
            $table->enum('request_status', RequestStatuses::toArray())->change();
        });
        Schema::useNativeSchemaOperationsIfPossible(false);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::useNativeSchemaOperationsIfPossible();
        Schema::table('employee_leaves', function (Blueprint $table) {
            $table->enum('request_status', ['Approved', 'Disapproved', 'Pending', 'Void', 'Denied'])->change();
        });
        DB::table('employee_leaves')->where('request_status', 'Disapproved')->update(['request_status' => RequestStatuses::DENIED->value]);
        Schema::table('employee_leaves', function (Blueprint $table) {
            $table->enum('request_status', ['Approved', 'Disapproved', 'Pending'])->change();
        });
        Schema::useNativeSchemaOperationsIfPossible(false);
    }
};

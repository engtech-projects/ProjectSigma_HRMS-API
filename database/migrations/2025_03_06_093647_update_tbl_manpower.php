<?php

use App\Enums\RequestStatuses;
use App\Enums\FillStatuses;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::useNativeSchemaOperationsIfPossible();
        Schema::table('manpower_requests', function (Blueprint $table) {
            $table->enum('request_status', ['Approved', 'Disapproved', 'Pending', 'Cancelled', 'Filled', 'Hold', 'Voided', 'Denied'])->after('breakdown_details')->change();
            $table->enum('fill_status', FillStatuses::toArray())->after('breakdown_details')->default(FillStatuses::PENDING->value);
        });

        DB::table('manpower_requests')->where('request_status', FillStatuses::CANCELLED->value)->update(['fill_status' => FillStatuses::CANCELLED->value]);
        DB::table('manpower_requests')->where('request_status', FillStatuses::FILLED->value)->update(['fill_status' => FillStatuses::FILLED->value]);
        DB::table('manpower_requests')->where('request_status', FillStatuses::HOLD->value)->update(['fill_status' => FillStatuses::HOLD->value]);

        DB::table('manpower_requests')->where('request_status', FillStatuses::CANCELLED->value)->update(['request_status' => RequestStatuses::VOID->value]);
        DB::table('manpower_requests')->where('request_status', FillStatuses::HOLD->value)->update(['request_status' => RequestStatuses::PENDING->value]);
        DB::table('manpower_requests')->where('request_status', FillStatuses::FILLED->value)->update(['request_status' => RequestStatuses::APPROVED->value]);
        DB::table('manpower_requests')->where('request_status', 'Disapproved')->update(['request_status' => RequestStatuses::DENIED->value]);

        Schema::table('manpower_requests', function (Blueprint $table) {
            $table->enum('request_status', RequestStatuses::toArray())->after('breakdown_details')->change();
        });


        Schema::useNativeSchemaOperationsIfPossible(false);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::useNativeSchemaOperationsIfPossible();

        Schema::table('manpower_requests', function (Blueprint $table) {
            $table->enum('request_status', ['Approved', 'Disapproved', 'Pending', 'Cancelled', 'Filled', 'Hold', 'Voided', 'Denied'])->change();
        });

        DB::table('manpower_requests')->where('request_status', 'Denied')->update(['request_status' => 'Disapproved']);
        DB::table('manpower_requests')->where('fill_status', FillStatuses::CANCELLED->value)->update(['request_status' => FillStatuses::CANCELLED->value]);
        DB::table('manpower_requests')->where('request_status', RequestStatuses::VOID->value)->update(['request_status' => FillStatuses::CANCELLED->value]);
        DB::table('manpower_requests')->where('fill_status', 'Filled')->update(['request_status' => FillStatuses::FILLED->value]);
        DB::table('manpower_requests')->where('fill_status', 'Hold')->update(['request_status' => FillStatuses::HOLD->value]);

        Schema::table('manpower_requests', function (Blueprint $table) {
            $table->dropColumn('fill_status');
        });

        Schema::table('manpower_requests', function (Blueprint $table) {
            $table->enum('request_status', ['Approved', 'Disapproved', 'Pending', 'Cancelled', 'Filled', 'Hold'])->change();
        });

        Schema::useNativeSchemaOperationsIfPossible(false);
    }
};

<?php

use App\Models\Project;
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
        // INDIRECT PROJECT ID
        // allowance_request - charge_assignment_type charge_assignment_id
        // att_port_assigns - assignment_type assignment_id
        // attendance_portals - assignment_type assignment_id
        // failure_to_logs - charging_type charging_id
        // payroll_details_charging - charge_type charge_id
        // request_13th_month_detail_amt - charge_type charge_id
        // request_13th_months - charging_type charging_id
        // travel_orders - charge_type charge_id

        // DIRECT PROJECT ID
        // employee_pan_request_projects - project_id
        // internal_work_experience_projects - project_id
        // payroll_records - project_id

        // SKIP
        // attendance_logs - project_id - cascaded on update
        // cash_advances - project_id - cascaded on update
        // employee_leaves - project_id - cascaded on update
        // overtime - project_id - cascaded on update
        // schedules - project_id - cascaded on update
        do {
            DB::transaction(function () {
                $usedIds = DB::table('projects')->pluck('id')->toArray();
                $projects = DB::table('projects')
                    ->whereRaw('id != project_monitoring_id')
                    ->whereNotIn('project_monitoring_id', $usedIds)
                    ->orderByDesc('project_monitoring_id')
                    ->get();
                foreach ($projects as $project) {
                    $originalId = $project->id;
                    $newId = $project->project_monitoring_id;
                    DB::table('projects')
                        ->where('id', $originalId)
                        ->update(['id' => $newId]);
                    // DIRECT PROJECT IDS
                    DB::table('employee_pan_request_projects')
                        ->where('project_id', $originalId)
                        ->update(['project_id' => $newId]);
                    DB::table('internal_work_experience_projects')
                        ->where('project_id', $originalId)
                        ->update(['project_id' => $newId]);
                    DB::table('payroll_records')
                        ->where('project_id', $originalId)
                        ->update(['project_id' => $newId]);
                    // INDIRECT PROJECT IDS
                    DB::table('allowance_request')
                        ->where('charge_assignment_type', 'App\\Models\\Project')
                        ->where('charge_assignment_id', $originalId)
                        ->update(['charge_assignment_id' => $newId]);
                    DB::table('att_port_assigns')
                        ->where('assignment_type', 'App\\Models\\Project')
                        ->where('assignment_id', $originalId)
                        ->update(['assignment_id' => $newId]);
                    DB::table('attendance_portals')
                        ->where('assignment_type', 'App\\Models\\Project')
                        ->where('assignment_id', $originalId)
                        ->update(['assignment_id' => $newId]);
                    DB::table('failure_to_logs')
                        ->where('charging_type', 'App\\Models\\Project')
                        ->where('charging_id', $originalId)
                        ->update(['charging_id' => $newId]);
                    DB::table('payroll_details_charging')
                        ->where('charge_type', 'App\\Models\\Project')
                        ->where('charge_id', $originalId)
                        ->update(['charge_id' => $newId]);
                    DB::table('request_13th_month_detail_amt')
                        ->where('charge_type', 'App\\Models\\Project')
                        ->where('charge_id', $originalId)
                        ->update(['charge_id' => $newId]);
                    DB::table('request_13th_months')
                        ->where('charging_type', 'App\\Models\\Project')
                        ->where('charging_id', $originalId)
                        ->update(['charging_id' => $newId]);
                    DB::table('travel_orders')
                        ->where('charge_type', 'App\\Models\\Project')
                        ->where('charge_id', $originalId)
                        ->update(['charge_id' => $newId]);
                }
            });

            $unmatchedProjects = Project::whereRaw('id != project_monitoring_id')
                ->get();
        } while ($unmatchedProjects->count() > 0);
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('project_monitoring_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

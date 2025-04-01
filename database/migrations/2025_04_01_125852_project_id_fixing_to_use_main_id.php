<?php

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
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->dropForeign('attendance_logs_project_id_foreign');
        });
        Schema::table('employee_leaves', function (Blueprint $table) {
            $table->dropForeign('employee_leaves_project_id_foreign');
        });
        Schema::table('overtime', function (Blueprint $table) {
            $table->dropForeign('overtime_project_id_foreign');
        });
        Schema::table('project_employees', function (Blueprint $table) {
            $table->dropForeign('project_employees_project_id_foreign');
        });
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign('schedules_project_id_foreign');
        });
        Schema::table('cash_advances', function (Blueprint $table) {
            $table->dropForeign('cash_advances_project_id_foreign');
        });
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->onUpdate("cascade")
                ->onDelete('restrict');
        });
        Schema::table('employee_leaves', function (Blueprint $table) {
            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->onUpdate("cascade")
                ->onDelete('restrict');
        });
        Schema::table('overtime', function (Blueprint $table) {
            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->onUpdate("cascade")
                ->onDelete('restrict');
        });
        Schema::table('project_employees', function (Blueprint $table) {
            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->onUpdate("cascade")
                ->onDelete('restrict');
        });
        Schema::table('schedules', function (Blueprint $table) {
            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->onUpdate("cascade")
                ->onDelete('restrict');
        });
        Schema::table('cash_advances', function (Blueprint $table) {
            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->onUpdate("cascade")
                ->onDelete('restrict');
        });
        try {
            // fix foreign key constraint fails
            DB::beginTransaction();
            $projects = DB::table('projects')->orderBy('id', 'desc')->get();
            foreach ($projects as $project) {
                // Process each $project
                DB::table('allowance_request')
                    ->where('charge_assignment_type', "App\Models\Project")
                    ->where('charge_assignment_id', $project->id)
                    ->update([
                        'charge_assignment_id' => $project->project_monitoring_id,
                    ]);
                DB::table('att_port_assigns')
                    ->where('assignment_type', "App\Models\Project")
                    ->where('assignment_id', $project->id)
                    ->update([
                        'assignment_id' => $project->project_monitoring_id,
                    ]);
                DB::table('failure_to_logs')
                    ->where('charging_type', "App\Models\Project")
                    ->where('charging_id', $project->id)
                    ->update([
                        'charging_id' => $project->project_monitoring_id,
                    ]);
                DB::table('payroll_details_charging')
                    ->where('charge_type', "App\Models\Project")
                    ->where('charge_id', $project->id)
                    ->update([
                        'charge_id' => $project->project_monitoring_id,
                    ]);
                DB::table('travel_orders')
                    ->where('charge_type', "App\Models\Project")
                    ->where('charge_id', $project->id)
                    ->update([
                        'charge_id' => $project->project_monitoring_id,
                    ]);
                // CODE BELOW IS REDUNDANT FROM FOREIGN CASCADE
                // BUT STILL APPLIED TO PREVENT FOREIGN KEY CONSTRAINT FAIL
                DB::table('attendance_logs')
                    ->where('project_id', $project->id)
                    ->update([
                        'project_id' => $project->project_monitoring_id,
                    ]);
                DB::table('cash_advances')
                    ->where('project_id', $project->id)
                    ->update([
                        'project_id' => $project->project_monitoring_id,
                    ]);
                DB::table('employee_leaves')
                    ->where('project_id', $project->id)
                    ->update([
                        'project_id' => $project->project_monitoring_id,
                    ]);
                DB::table('employee_pan_request_projects')
                    ->where('project_id', $project->id)
                    ->update([
                        'project_id' => $project->project_monitoring_id,
                    ]);
                DB::table('internal_work_experience_projects')
                    ->where('project_id', $project->id)
                    ->update([
                        'project_id' => $project->project_monitoring_id,
                    ]);
                DB::table('overtime')
                    ->where('project_id', $project->id)
                    ->update([
                        'project_id' => $project->project_monitoring_id,
                    ]);
                DB::table('payroll_records')
                    ->where('project_id', $project->id)
                    ->update([
                        'project_id' => $project->project_monitoring_id,
                    ]);
                DB::table('project_employees')
                    ->where('project_id', $project->id)
                    ->update([
                        'project_id' => $project->project_monitoring_id,
                    ]);
                DB::table('schedules')
                    ->where('project_id', $project->id)
                    ->update([
                        'project_id' => $project->project_monitoring_id,
                    ]);

                DB::table('projects')
                    ->where('id', $project->id)
                    ->update([
                        'id' => $project->project_monitoring_id,
                    ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->dropForeign('attendance_logs_project_id_foreign');
        });
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->onUpdate("restrict")
                ->onDelete('restrict');
        });
        Schema::table('employee_leaves', function (Blueprint $table) {
            $table->dropForeign('employee_leaves_project_id_foreign');
        });
        Schema::table('employee_leaves', function (Blueprint $table) {
            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->onUpdate("restrict")
                ->onDelete('restrict');
        });
        Schema::table('overtime', function (Blueprint $table) {
            $table->dropForeign('overtime_project_id_foreign');
        });
        Schema::table('overtime', function (Blueprint $table) {
            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->onUpdate("restrict")
                ->onDelete('restrict');
        });
        Schema::table('project_employees', function (Blueprint $table) {
            $table->dropForeign('project_employees_project_id_foreign');
        });
        Schema::table('project_employees', function (Blueprint $table) {
            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->onUpdate("restrict")
                ->onDelete('restrict');
        });
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign('schedules_project_id_foreign');
        });
        Schema::table('schedules', function (Blueprint $table) {
            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->onUpdate("restrict")
                ->onDelete('restrict');
        });
        Schema::table('cash_advances', function (Blueprint $table) {
            $table->dropForeign('cash_advances_project_id_foreign');
        });
        Schema::table('cash_advances', function (Blueprint $table) {
            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->onUpdate("restrict")
                ->onDelete('restrict');
        });
    }
};

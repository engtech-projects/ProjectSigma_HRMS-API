<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccessibilitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('accessibilities')->insert(
            [
                [
                    'id' => 1,
                    'accessibilities_name' => "hrms:dashboard",
                    'deleted_at' => null,
                    'created_at' => '2024-01-15 01:52:02',
                    'updated_at' => '2024-01-15 01:52:02',
                ],
                [
                    'id' => 2,
                    'accessibilities_name' => "hrms:announcement",
                    'deleted_at' => null,
                    'created_at' => '2024-01-15 01:52:02',
                    'updated_at' => '2024-01-15 01:52:02',
                ],
                [
                    'id' => 3,
                    'accessibilities_name' => "hrms:attendance_attendance_portal",
                    'deleted_at' => null,
                    'created_at' => '2024-01-15 01:52:02',
                    'updated_at' => '2024-01-15 01:52:02',
                ],
                [
                    'id' => 4,
                    'accessibilities_name' => "hrms:attendance_daily_logs_record",
                    'deleted_at' => null,
                    'created_at' => '2024-01-15 01:52:02',
                    'updated_at' => '2024-01-15 01:52:02',
                ],
                [
                    'id' => 5,
                    'accessibilities_name' => "hrms:attendance_failure_to_log",
                    'deleted_at' => null,
                    'created_at' => '2024-01-15 01:52:02',
                    'updated_at' => '2024-01-15 01:52:02',
                ],
                [
                    'id' => 6,
                    'accessibilities_name' => "hrms:attendance_face_recognition",
                    'deleted_at' => null,
                    'created_at' => '2024-01-15 01:52:02',
                    'updated_at' => '2024-01-15 01:52:02',
                ],
                [
                    'id' => 7,
                    'accessibilities_name' => "hrms:attendance_attendance_login",
                    'deleted_at' => null,
                    'created_at' => '2024-01-15 01:52:02',
                    'updated_at' => '2024-01-15 01:52:02',
                ],
                [
                    'id' => 8,
                    'accessibilities_name' => "hrms:attendance_qr_code",
                    'deleted_at' => null,
                    'created_at' => '2024-01-15 01:52:02',
                    'updated_at' => '2024-01-15 01:52:02',
                ],
                [
                    'id' => 9,
                    'accessibilities_name' => "hrms:event_calendar",
                    'deleted_at' => null,
                    'created_at' => '2024-01-15 01:52:02',
                    'updated_at' => '2024-01-15 01:52:02',
                ],
                [
                    'id' => 10,
                    'accessibilities_name' => "hrms:employee_201",
                    'deleted_at' => null,
                    'created_at' => '2024-01-15 01:52:02',
                    'updated_at' => '2024-01-15 01:52:02',
                ],
                [
                    'id' => 11,
                    'accessibilities_name' => "hrms:employee_personnel_action_notice",
                    'deleted_at' => null,
                    'created_at' => '2024-01-15 01:52:02',
                    'updated_at' => '2024-01-15 01:52:02',
                ],
                [
                    'id' => 12,
                    'accessibilities_name' => "hrms:employee_onboarding",
                    'deleted_at' => null,
                    'created_at' => '2024-01-15 01:52:02',
                    'updated_at' => '2024-01-15 01:52:02',
                ],
                [
                    'id' => 13,
                    'accessibilities_name' => "hrms:employee_manpower_request",
                    'deleted_at' => null,
                    'created_at' => '2024-01-15 01:52:02',
                    'updated_at' => '2024-01-15 01:52:02',
                ],
                [
                    'id' => 14,
                    'accessibilities_name' => "hrms:setup_user_account",
                    'deleted_at' => null,
                    'created_at' => '2024-01-15 01:52:02',
                    'updated_at' => '2024-01-15 01:52:02',
                ],
                [
                    'id' => 15,
                    'accessibilities_name' => "hrms:setup_department",
                    'deleted_at' => null,
                    'created_at' => '2024-01-15 01:52:02',
                    'updated_at' => '2024-01-15 01:52:02',
                ],
                [
                    'id' => 17,
                    'accessibilities_name' => "hrms:setup_approvals",
                    'deleted_at' => null,
                    'created_at' => '2024-01-15 01:52:02',
                    'updated_at' => '2024-01-15 01:52:02',
                ],
                [
                    'id' => 18,
                    'accessibilities_name' => "hrms:setup_hmo",
                    'deleted_at' => null,
                    'created_at' => '2024-01-15 01:52:02',
                    'updated_at' => '2024-01-15 01:52:02',
                ],
                [
                    'id' => 19,
                    'accessibilities_name' => "hrms:setup_pag_ibig",
                    'deleted_at' => null,
                    'created_at' => '2024-01-15 01:52:02',
                    'updated_at' => '2024-01-15 01:52:02',
                ],
                [
                    'id' => 20,
                    'accessibilities_name' => "hrms:setup_phil_health",
                    'deleted_at' => null,
                    'created_at' => '2024-01-15 01:52:02',
                    'updated_at' => '2024-01-15 01:52:02',
                ],
                [
                    'id' => 21,
                    'accessibilities_name' => "hrms:setup_sss",
                    'deleted_at' => null,
                    'created_at' => '2024-01-15 01:52:02',
                    'updated_at' => '2024-01-15 01:52:02',
                ],
                [
                    'id' => 22,
                    'accessibilities_name' => "hrms:setup_with_holding_tax",
                    'deleted_at' => null,
                    'created_at' => '2024-01-15 01:52:02',
                    'updated_at' => '2024-01-15 01:52:02',
                ],
                [
                    'id' => 23,
                    'accessibilities_name' => "hrms:setup_leaves",
                    'deleted_at' => null,
                    'created_at' => '2024-01-15 01:52:02',
                    'updated_at' => '2024-01-15 01:52:02',
                ],
                [
                    'id' => 24,
                    'accessibilities_name' => "hrms:leave",
                    'deleted_at' => null,
                    'created_at' => '2024-01-15 01:52:02',
                    'updated_at' => '2024-01-15 01:52:02',
                ],
                [
                    'id' => 25,
                    'accessibilities_name' => "hrms:loans_and_advances_cash_advance",
                    'deleted_at' => null,
                    'created_at' => '2024-01-15 01:52:02',
                    'updated_at' => '2024-01-15 01:52:02',
                ],
                [
                    'id' => 26,
                    'accessibilities_name' => "hrms:loans_and_advances_loans",
                    'deleted_at' => null,
                    'created_at' => '2024-01-15 01:52:02',
                    'updated_at' => '2024-01-15 01:52:02',
                ],
                [
                    'id' => 27,
                    'accessibilities_name' => "hrms:loans_and_advances_other_deductions",
                    'deleted_at' => null,
                    'created_at' => '2024-01-15 01:52:02',
                    'updated_at' => '2024-01-15 01:52:02',
                ],
                [
                    'id' => 28,
                    'accessibilities_name' => "hrms:overtime",
                    'deleted_at' => null,
                    'created_at' => '2024-01-15 01:52:02',
                    'updated_at' => '2024-01-15 01:52:02',
                ],
                [
                    'id' => 29,
                    'accessibilities_name' => "hrms:payroll_generate_payroll",
                    'deleted_at' => null,
                    'created_at' => '2024-01-15 01:52:02',
                    'updated_at' => '2024-01-15 01:52:02',
                ],
                [
                    'id' => 30,
                    'accessibilities_name' => "hrms:payroll_13th_month",
                    'deleted_at' => null,
                    'created_at' => '2024-01-15 01:52:02',
                    'updated_at' => '2024-01-15 01:52:02',
                ],
                [
                    'id' => 31,
                    'accessibilities_name' => "hrms:payroll_allowance",
                    'deleted_at' => null,
                    'created_at' => '2024-01-15 01:52:02',
                    'updated_at' => '2024-01-15 01:52:02',
                ],
                [
                    'id' => 32,
                    'accessibilities_name' => "hrms:payroll_payroll_report",
                    'deleted_at' => null,
                    'created_at' => '2024-01-15 01:52:02',
                    'updated_at' => '2024-01-15 01:52:02',
                ],
                [
                    'id' => 33,
                    'accessibilities_name' => "hrms:reports",
                    'deleted_at' => null,
                    'created_at' => '2024-01-15 01:52:02',
                    'updated_at' => '2024-01-15 01:52:02',
                ],
                [
                    'id' => 34,
                    'accessibilities_name' => "hrms:schedule_department_schedule",
                    'deleted_at' => null,
                    'created_at' => '2024-01-15 01:52:02',
                    'updated_at' => '2024-01-15 01:52:02',
                ],
                [
                    'id' => 35,
                    'accessibilities_name' => "hrms:schedule_employee_schedule",
                    'deleted_at' => null,
                    'created_at' => '2024-01-15 01:52:02',
                    'updated_at' => '2024-01-15 01:52:02',
                ],
                [
                    'id' => 36,
                    'accessibilities_name' => "hrms:schedule_project_schedule",
                    'deleted_at' => null,
                    'created_at' => '2024-01-15 01:52:02',
                    'updated_at' => '2024-01-15 01:52:02',
                ],
            ]
        );
    }
}

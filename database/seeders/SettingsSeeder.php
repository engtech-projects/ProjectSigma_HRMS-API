<?php

namespace Database\Seeders;

use App\Enums\AttendanceSettings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('settings')->upsert(
            [
                [
                    'id' => 1,
                    'setting_name' => AttendanceSettings::LATE_ALLOWANCE->value,
                    'value' => '15',
                ],
                [
                    'id' => 2,
                    'setting_name' => AttendanceSettings::LATE_ABSENT->value,
                    'value' => '30',
                ],
                [
                    'id' => 3,
                    'setting_name' => AttendanceSettings::PAYROLL_20TH_LOCKUP_DAY_LIMIT->value,
                    'value' => '12',
                ],
                [
                    'id' => 4,
                    'setting_name' => AttendanceSettings::PAYROLL_20TH_LOCKUP_SCHEDULE_DAY_OF_MONTH->value,
                    'value' => '13',
                ],
                [
                    'id' => 5,
                    'setting_name' => AttendanceSettings::PAYROLL_20TH_LOCKUP_SCHEDULE_TIME_OF_DAY->value,
                    'value' => '10:00',
                ],
                [
                    'id' => 6,
                    'setting_name' => AttendanceSettings::PAYROLL_5TH_LOCKUP_DAY_LIMIT->value,
                    'value' => '27',
                ],
                [
                    'id' => 7,
                    'setting_name' => AttendanceSettings::PAYROLL_5TH_LOCKUP_SCHEDULE_DAY_OF_MONTH->value,
                    'value' => '28',
                ],
                [
                    'id' => 8,
                    'setting_name' => AttendanceSettings::PAYROLL_5TH_LOCKUP_SCHEDULE_TIME_OF_DAY->value,
                    'value' => '10:00',
                ],
            ],
            [
                'id'
            ],
            [
                'setting_name'
            ]
        );
    }
}

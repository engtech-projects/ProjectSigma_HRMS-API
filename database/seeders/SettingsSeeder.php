<?php

namespace Database\Seeders;

use App\Enums\SetupSettingsEnums;
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
                    'setting_name' => SetupSettingsEnums::LATE_ALLOWANCE->value,
                    'value' => '15',
                ],
                [
                    'id' => 2,
                    'setting_name' => SetupSettingsEnums::LATE_ABSENT->value,
                    'value' => '30',
                ],
                [
                    'id' => 3,
                    'setting_name' => SetupSettingsEnums::PAYROLL_20TH_LOCKUP_DAY_LIMIT->value,
                    'value' => '12',
                ],
                [
                    'id' => 4,
                    'setting_name' => SetupSettingsEnums::PAYROLL_20TH_LOCKUP_SCHEDULE_DAY_OF_MONTH->value,
                    'value' => '13',
                ],
                [
                    'id' => 5,
                    'setting_name' => SetupSettingsEnums::PAYROLL_20TH_LOCKUP_SCHEDULE_TIME_OF_DAY->value,
                    'value' => '10:00',
                ],
                [
                    'id' => 6,
                    'setting_name' => SetupSettingsEnums::PAYROLL_5TH_LOCKUP_DAY_LIMIT->value,
                    'value' => '27',
                ],
                [
                    'id' => 7,
                    'setting_name' => SetupSettingsEnums::PAYROLL_5TH_LOCKUP_SCHEDULE_DAY_OF_MONTH->value,
                    'value' => '28',
                ],
                [
                    'id' => 8,
                    'setting_name' => SetupSettingsEnums::USER_201_EDITOR->value,
                    'value' => '10:00',
                ],
                [
                    'id' => 9,
                    'setting_name' => SetupSettingsEnums::USER_SALARY_GRADE_SETTER->value,
                    'value' => '10:00',
                ],
                [
                    'id' => 10,
                    'setting_name' => SetupSettingsEnums::LOGOUT_CHANGE_PASSWORD->value,
                    'value' => '10:00',
                ],
                [
                    'id' => 11,
                    'setting_name' => SetupSettingsEnums::SINGLE_DEVICE_LOGIN->value,
                    'value' => '10:00',
                ],
                [
                    'id' => 12,
                    'setting_name' => SetupSettingsEnums::EARLY_LOGIN->value,
                    'value' => '10:00',
                ],
                [
                    'id' => 13,
                    'setting_name' => SetupSettingsEnums::LATE_LOGOUT->value,
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

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
                ]
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

<?php

namespace Database\Seeders;

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
                    'setting_name' => "Late allowance min",
                    'value' => '15',
                ],
                [
                    'id' => 2,
                    'setting_name' => "Late halfday min",
                    'value' => '30',
                ]
            ],
            [
                'id', 'setting_name'
            ],
            [
                'value'
            ]
        );
    }
}

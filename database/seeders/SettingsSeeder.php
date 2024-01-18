<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('settings')->insert(
            [
                'id' => 1,
                'setting_name' => "Late_allowance_min",
                'value' => '15',
            ],
            [
                'id' => 2,
                'setting_name' => "Late_halfday_min",
                'value' => '30',
            ]
        );
    }
}

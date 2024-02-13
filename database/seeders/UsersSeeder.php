<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert(
            [
                [
                    'id' => 1,
                    'name' => "admin",
                    'email' => 'admin@gmail.com',
                    'password' => Hash::make('!onetimepassword'),
                    'accessibilities' => '[1]'
                ],
                [
                    'id' => 2,
                    'name' => "Jermily C. Mozo",
                    'email' => 'jermilymozo@gmail.com',
                    'password' => Hash::make('!onetimepassword'),
                    'accessibilities' => '[1]'
                ],
                [
                    'id' => 3,
                    'name' => "ENGR. Richie C. Dalauta",
                    'email' => 'richiedalauta@gmail.com',
                    'password' => Hash::make('!onetimepassword'),
                    'accessibilities' => '[1]'
                ],
                [
                    'id' => 4,
                    'name' => "ENGR. Dionision Jonas A. Rodes",
                    'email' => 'dionisionrodes@gmail.com',
                    'password' => Hash::make('!onetimepassword'),
                    'accessibilities' => '[1]'
                ],
                [
                    'id' => 5,
                    'name' => "ENGR. Angel A. Abrau",
                    'email' => 'angelabrau@gmail.com',
                    'password' => Hash::make('!onetimepassword'),
                    'accessibilities' => '[1]'
                ],
            ]
        );
    }
}

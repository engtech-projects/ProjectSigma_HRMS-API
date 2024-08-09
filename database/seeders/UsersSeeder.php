<?php

namespace Database\Seeders;

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
        DB::table('users')->upsert(
            [
                [
                    'id' => 1,
                    'name' => "admin",
                    'email' => 'admin@gmail.com',
                    'password' => Hash::make('!onetimepassword'),
                    'accessibilities' => json_encode(range(1, 36)),
                    'type' => 'Administrator',
                ]
            ],
            [ "id" ],
            [ "name" ]
        );
    }
}

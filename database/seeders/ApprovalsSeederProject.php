<?php

namespace Database\Seeders;

use App\Enums\ApprovalModules;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApprovalsSeederProject extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('approvals')->upsert(
            [
                [
                    'id' => 3010,
                    'form' => "Project Award Request",
                    'approvals' => "[]",
                    'module' => ApprovalModules::PROJECT->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3020,
                    'form' => "Schedule for Submission",
                    'approvals' => "[]",
                    'module' => ApprovalModules::PROJECT->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3030,
                    'form' => "Bill of Materials",
                    'approvals' => "[]",
                    'module' => ApprovalModules::PROJECT->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3040,
                    'form' => "Direct Cost",
                    'approvals' => "[]",
                    'module' => ApprovalModules::PROJECT->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3050,
                    'form' => "Detailed Estimates",
                    'approvals' => "[]",
                    'module' => ApprovalModules::PROJECT->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            ],
            [
                'id'
            ],
            [
                'form',
                'module',
                'deleted_at',
            ]
        );
    }
}

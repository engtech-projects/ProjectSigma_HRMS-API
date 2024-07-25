<?php

namespace Database\Seeders;

use App\Enums\ApprovalModules;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ApprovalsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //HRMS
        DB::table('approvals')->upsert(
            [
                [
                    'id' => 1,
                    'form' => "Personnel Action Notice",
                    'module' => ApprovalModules::HRMS->value,
                    'approvals' => "[]",
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2,
                    'form' => "Manpower Request",
                    'module' => ApprovalModules::HRMS->value,
                    'approvals' => "[]",
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 3,
                    'form' => "Overtime",
                    'module' => ApprovalModules::HRMS->value,
                    'approvals' => "[]",
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 4,
                    'form' => "Leave",
                    'module' => ApprovalModules::HRMS->value,
                    'approvals' => '[]',
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 5,
                    'form' => "Cash Advance",
                    'module' => ApprovalModules::HRMS->value,
                    'approvals' => "[]",
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 6,
                    'form' => "Travel Order",
                    'module' => ApprovalModules::HRMS->value,
                    'approvals' => "[]",
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 7,
                    'form' => "Generate Allowance",
                    'module' => ApprovalModules::HRMS->value,
                    'approvals' => "[]",
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 8,
                    'form' => "Payroll",
                    'module' => ApprovalModules::HRMS->value,
                    'approvals' => "[]",
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 9,
                    'form' => "13th Month",
                    'module' => ApprovalModules::HRMS->value,
                    'approvals' => "[]",
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 10,
                    'form' => "Failure To Log",
                    'module' => ApprovalModules::HRMS->value,
                    'approvals' => "[]",
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],

            ],
            [
                'id'
            ],
            [
                'form'
            ]
        );
        //INVENTORY
        DB::table('approvals')->upsert(
            [
                [
                    'id' => 210,
                    'form' => "New Item Profile",
                    'approvals' => "[]",
                    'module' => ApprovalModules::INVENTORY->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            ],
            [
                'id'
            ],
            [
                'form'
            ]
        );
        //ACCOUNTING
        // DB::table('approvals')->upsert(
        //     [
        //         [
        //             'id' => 210,
        //             'form' => "New Item Profile",
        //             'approvals' => "[]",
        //             'module' => ApprovalModules::INVENTORY->value,
        //             'deleted_at' => null,
        //             'created_at' => Carbon::now(),
        //             'updated_at' => Carbon::now(),
        //         ],
        //     ],
        //     [
        //         'id'
        //     ],
        //     [
        //         'form'
        //     ]
        // );
        //PROJECTS
        // DB::table('approvals')->upsert(
        //     [
        //         [
        //             'id' => 210,
        //             'form' => "New Item Profile",
        //             'approvals' => "[]",
        //             'module' => ApprovalModules::INVENTORY->value,
        //             'deleted_at' => null,
        //             'created_at' => Carbon::now(),
        //             'updated_at' => Carbon::now(),
        //         ],
        //     ],
        //     [
        //         'id'
        //     ],
        //     [
        //         'form'
        //     ]
        // );
    }
}

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
                    'form' => "13th Month Request",
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
                [
                    'id' => 11,
                    'form' => "Salary Disbursement",
                    'module' => ApprovalModules::HRMS->value,
                    'approvals' => "[]",
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 12,
                    'form' => "Void Requests",
                    'module' => ApprovalModules::HRMS->value,
                    'approvals' => "[]",
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 20,
                    'form' => "",
                    'module' => ApprovalModules::HRMS->value,
                    'approvals' => "[]",
                    'deleted_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                // USE 20 when creating new forms and when used delete this comments
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
                [
                    'id' => 220,
                    'form' => "Request BOM",
                    'approvals' => "[]",
                    'module' => ApprovalModules::INVENTORY->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 230,
                    'form' => "Supplier Accreditation",
                    'approvals' => "[]",
                    'module' => ApprovalModules::INVENTORY->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 240,
                    'form' => "Requisition Slip",
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
                'form',
                'module',
                'deleted_at',
            ]
        );
        //ACCOUNTING
        DB::table('approvals')->upsert(
            [
                [
                    'id' => 2010,
                    'form' => "Payment Request Form (NPO)",
                    'approvals' => "[]",
                    'module' => ApprovalModules::ACCOUNTING->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2020,
                    'form' => "Payment Request Form (PO)",
                    'approvals' => "[]",
                    'module' => ApprovalModules::ACCOUNTING->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2030,
                    'form' => "Disbursement Voucher Request",
                    'approvals' => "[]",
                    'module' => ApprovalModules::ACCOUNTING->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2040,
                    'form' => "Cash Voucher Request",
                    'approvals' => "[]",
                    'module' => ApprovalModules::ACCOUNTING->value,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id' => 2050,
                    'form' => "Payment Request Form (PAYROLL)",
                    'approvals' => "[]",
                    'module' => ApprovalModules::ACCOUNTING->value,
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
        // PROJECTS
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

<?php

namespace App\Console\Commands;

use App\Http\Services\ApiServices\AccountingSecretkeyService;
use App\Models\RequestSalaryDisbursement;
use Illuminate\Console\Command;

class FunctionDev extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:function-dev';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command To test Functions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $requestSalaryDisbursement = RequestSalaryDisbursement::where('id', 17)->first();
        $accountingService = new AccountingSecretkeyService();
        $accountingService->submitPayrollRequest($requestSalaryDisbursement);

    }
}

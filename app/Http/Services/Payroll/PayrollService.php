<?php

namespace App\Http\Services\Payroll;

use App\Http\Services\Payroll\PayrollDeduction;
use App\Http\Services\EmployeeService;
use App\Http\Traits\Attendance;
use App\Models\PayrollRecord;

class PayrollService
{

    protected $payrollRequest;
    public function __construct(PayrollRecord $payrollRequest)
    {
        $this->payrollRequest = $payrollRequest;
    }

    public function getAll()
    {
        return PayrollRecord::with('payroll_details')->get()->append(['charging_name']);
    }

    public function getMyApprovals()
    {
        $userId = auth()->user()->id;
        $result = PayrollRecord::requestStatusPending()
            ->authUserPending()
            ->get();
        return $result->filter(function ($item) use ($userId) {
            $nextPendingApproval = $item->getNextPendingApproval();
            return ($nextPendingApproval && $userId === $nextPendingApproval['user_id']);
        });
    }

}

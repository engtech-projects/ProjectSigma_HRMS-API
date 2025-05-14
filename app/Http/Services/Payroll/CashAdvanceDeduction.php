<?php

namespace App\Http\Services\Payroll;

use App\Models\Employee;
use Carbon\Carbon;

class CashAdvanceDeduction extends PayrollDeduction
{
    protected $filter;
    public $employee;
    public $salary;
    public $cashAdvance;
    public function __construct(Employee $employee, $salary, array $filters)
    {

        $this->employee = $employee;
        $this->cashAdvance = $this->getCashAdvanceDeduction($filters);
        $this->filter = $filters;
    }
    public function getCashAdvanceDeduction($filters)
    {
        $deduction = 0;
        $payrollDate = Carbon::parse($filters["payroll_date"]);
        $cashAdvance = $this->employee->cash_advance()->isApproved()->first();

        if ($cashAdvance) {
            if (!$cashAdvance->cashPaid()) {
                if ($cashAdvance->deduction_date_start->lt($payrollDate)) {
                    $deduction = $cashAdvance->installment_deduction;
                }
            }
            if ($filters["payroll_type"] === "weekly") {
                $deduction = $deduction / 4;
            } else {
                $deduction = $deduction / 2;
            }
        }
        return $deduction;
    }
}

<?php

namespace App\Http\Services\Payroll;

use App\Models\Employee;
use Carbon\Carbon;

class LoanDeduction extends PayrollDeduction
{
    protected $filter;
    public $employee;
    public $salary;
    public $loan;
    public function __construct(Employee $employee, $salary, array $filters)
    {

        $this->employee = $employee;
        $this->loan = $this->getLoanDeduction($filters);
        $this->filter = $filters;
    }
    public function getLoanDeduction($filters)
    {
        $deduction = 0;
        $payrollDate = Carbon::parse($filters["payroll_date"]);
        $loan = $this->employee->employee_loan->first();
        if ($loan) {
            $loanPayments = $loan->loan_payment_notposted;
            if (!$loan->loanPaid()) {
                if ($loan->deduction_date_start->lt($payrollDate)) {
                    $deduction = $loan->installment_deduction;
                }
                if ($filters["payroll_type"] === "weekly") {
                    $deduction = $deduction / 4;
                } else {
                    $deduction = $deduction / 2;
                }
            }
        }
        return $deduction;
    }
}

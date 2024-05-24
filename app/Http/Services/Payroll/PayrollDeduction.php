<?php

namespace App\Http\Services\Payroll;

use App\Models\Employee;
use App\Models\SSSContribution;
use App\Models\PagibigContribution;
use App\Models\PhilhealthContribution;
use App\Models\WitholdingTaxContribution;
use App\Http\Services\Payroll\CashAdvanceDeduction;
use Exception;

class PayrollDeduction
{
    public $sss;
    public $philhealth;
    public $loan;
    public $pagibig;
    public $employee;
    public $filters;
    public $salary;
    public $cashAdvance;
    public $withHoldingTax;
    public function __construct(Employee $employee, $salary = 0, array $filters)
    {
        $this->salary = $salary;
        $this->employee = $employee;
        $this->filters = $filters;
        $this->sss = $this->SSSDeduction();
        $this->philhealth = $this->PhilhealthDeduction();
        $this->pagibig = $this->PagibigDeduction();
        $this->loan = $this->LoanDeduction();
        $this->cashAdvance = $this->CashAdvanceDeduction();
        $this->withHoldingTax = $this->WithHoldingTaxDeduction();
    }
    private function SSSDeduction()
    {
        $result = [];
        $sss = SSSContribution::getContribution($this->salary);
        if ($sss) {
            $contribution = $this->getContributionTotal([
                "employer" => $sss->employer_contribution,
                "employee" => $sss->employee_contribution
            ]);
            $compensation = $this->getCompensationTotal([
                "employer" => $sss->employer_share,
                "employee" => $sss->employee_share
            ]);

            $result = [
                "employer_contribution" => $contribution["employer"],
                "employee_contribution" =>  $contribution["employee"],
                "employer_compensation" => $compensation["employer"],
                "employee_compensation" => $compensation["employee"],
                "total_contribution" => $contribution["employer"] + $contribution["employee"],
                "total_compensation" => $compensation["employer"] + $compensation["employee"]
            ];
        }
        return $result;
    }
    private function PhilhealthDeduction()
    {
        $result = [];
        $philhealth = PhilhealthContribution::getContribution($this->salary);
        if ($philhealth) {
            if ($philhealth->share_type == 'Amount') {
                $employeeCompensation = $philhealth->employee_share;
                $employeerCompensation = $philhealth->employer_share;
            } else {
                $employeeCompensation = ($philhealth->employee_share / 100) * $this->salary;
                $employeerCompensation = ($philhealth->employer_share / 100) * $this->salary;
            }
            $compensation = $this->getCompensationTotal([
                "employer" => $employeerCompensation,
                "employee" => $employeeCompensation
            ]);
            $result = [
                "share_type" => $philhealth->share_type,
                "employer_compensation" => $compensation["employer"],
                "employee_compensation" => $compensation["employee"],
                "total_compensation" => $compensation["employer"] + $compensation["employee"]
            ];
        }


        return $result;
    }
    private function PagibigDeduction()
    {
        $result = [];
        $pagibig = PagibigContribution::getContribution($this->salary);

        if ($pagibig) {
            $employeeCompensation = ($pagibig->employee_share_percent / 100) * $this->salary;
            $employeerCompensation = ($pagibig->employer_share_percent / 100) * $this->salary;

            $compensation = $this->getCompensationTotal([
                "employer" => $employeerCompensation,
                "employee" => $employeeCompensation
            ]);
            $result = [
                "employer_compensation" => $compensation["employer"] > $pagibig->employer_maximum_contribution ?
                    $pagibig->employer_maximum_contribution : $compensation["employer"],
                "employee_compensation" => $compensation["employee"] > $pagibig->employee_maximum_contribution ?
                    $pagibig->employee_maximum_contribution : $compensation["employee"],
                "total_compensation" => $compensation["employer"] + $compensation["employee"]
            ];
        }
        return $result;
    }

    private function getCompensationTotal($compensation)
    {
        if ($compensation) {
            if ($this->filters["payroll_type"] == "weekly") {
                $compensation["employee"] =  $compensation["employee"] / 4;
                $compensation["employer"] =  $compensation["employer"] / 4;
            } else {
                $compensation["employee"] =  $compensation["employee"] / 2;
                $compensation["employer"] =  $compensation["employer"] / 2;
            }
        }
        return $compensation;
    }

    private function getContributionTotal($contribution)
    {
        if ($contribution) {
            if ($this->filters["payroll_type"] == "weekly") {
                $contribution["employee"] =  $contribution["employee"] / 4;
                $contribution["employer"] =  $contribution["employer"] / 4;
            } else {
                $contribution["employee"] =  $contribution["employee"] / 2;
                $contribution["employer"] =  $contribution["employer"] / 2;
            }
        }
        return $contribution;
    }
    public function LoanDeduction()
    {
        $loan = new LoanDeduction($this->employee, $this->salary, $this->filters);
        return $loan;
    }
    public function CashAdvanceDeduction()
    {
        $cashAdvance = new CashAdvanceDeduction($this->employee, $this->salary, $this->filters);
        return $cashAdvance;
    }

    public function WithHoldingTaxDeduction()
    {
        $wht = WitholdingTaxContribution::getContribution($this->salary);
        $taxBase = $wht->tax_base;
        $taxAmount = $wht->tax_amount;
        $diff = abs($taxBase - $taxAmount);
        $total = ($wht->tax_percent_over_base / 100) * $diff + $taxAmount;
        return $total;
    }
}

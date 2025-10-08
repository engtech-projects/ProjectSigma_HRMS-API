<?php

namespace App\Http\Services\Payroll;

use App\Models\Employee;
use App\Models\SSSContribution;
use App\Models\PagibigContribution;
use App\Models\PhilhealthContribution;
use App\Models\WitholdingTaxContribution;

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

        $result = [
            "employer_contribution" => 0,
            "employee_contribution" =>  0,
            "employer_compensation" => 0,
            "employee_compensation" => 0,
            "employer_wisp" => 0,
            "employee_wisp" => 0,
            "total_contribution" => 0,
            "total_compensation" => 0,
            "total_wisp" => 0,
        ];
        if ($sss) {
            $contribution = $this->getContributionTotal([
                "employer" => $sss->employer_share,
                "employee" => $sss->employee_share
            ]);
            $compensation = $this->getCompensationTotal([
                "employer" => $sss->employer_compensation,
                "employee" => $sss->employee_compensation
            ]);
            $wisp = $this->getCompensationTotal([
                "employer" => $sss->employer_wisp,
                "employee" => $sss->employee_wisp
            ]);

            $result = [
                "employer_contribution" => $contribution["employer"],
                "employee_contribution" =>  $contribution["employee"],
                "employer_compensation" => $compensation["employer"],
                "employee_compensation" => $compensation["employee"],
                "employer_wisp" => $wisp["employer"],
                "employee_wisp" => $wisp["employee"],
                "total_contribution" => $contribution["employer"] + $contribution["employee"],
                "total_compensation" => $compensation["employer"] + $compensation["employee"],
                "total_wisp" => $wisp["employer"] + $wisp["employee"],
            ];
        }
        return $result;
    }
    private function PhilhealthDeduction()
    {
        $philhealth = PhilhealthContribution::getContribution($this->salary);
        $result = [
            "share_type" => 0,
            "employer_contribution" => 0,
            "employee_contribution" => 0,
            "total_contribution" => 0,
        ];
        if ($philhealth) {
            if ($philhealth->share_type == 'Amount') {
                $employeeContribution = $philhealth->employee_share;
                $employeerContribution = $philhealth->employer_share;
            } else {
                $employeeContribution = ($philhealth->employee_share / 100) * $this->salary;
                $employeerContribution = ($philhealth->employer_share / 100) * $this->salary;
            }
            $contribution = $this->getContributionTotal([
                "employer" => $employeerContribution,
                "employee" => $employeeContribution
            ]);
            $result = [
                "share_type" => $philhealth->share_type,
                "employer_contribution" => $contribution["employer"],
                "employee_contribution" => $contribution["employee"],
                "total_contribution" => $contribution["employer"] + $contribution["employee"]
            ];
        }

        return $result;
    }
    private function PagibigDeduction()
    {
        $result = [];
        $pagibig = PagibigContribution::getContribution($this->salary);
        $result = [
            "employer_contribution" => 0,
            "employee_contribution" => 0,
            "total_contribution" => 0,
        ];
        if ($pagibig) {
            $employeeContribution = ($pagibig->employee_share_percent / 100) * $this->salary;
            $employeerContribution = ($pagibig->employer_share_percent / 100) * $this->salary;
            $contribution = $this->getContributionTotal([
                "employer" => $employeerContribution,
                "employee" => $employeeContribution
            ]);
            $result = [
                "employer_contribution" => $contribution["employer"] > $pagibig->employer_maximum_contribution ?
                    $pagibig->employer_maximum_contribution : $contribution["employer"],
                "employee_contribution" => $contribution["employee"] > $pagibig->employee_maximum_contribution ?
                    $pagibig->employee_maximum_contribution : $contribution["employee"],
                "total_contribution" => $contribution["employer"] + $contribution["employee"]
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

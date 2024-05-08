<?php

namespace App\Http\Services;

use App\Models\PagibigContribution;
use App\Models\PhilhealthContribution;
use App\Models\SSSContribution;

class PayrollDeduction
{
    public $sss;
    public $philhealth;
    public $pagibig;
    private $filters;
    private $salary;
    public function __construct($salary = 0, array $filters)
    {
        $this->salary = $salary;
        $this->filters = $filters;
        $this->sss = $this->SSSDeduction();
        $this->philhealth = $this->PhilhealthDeduction();
        $this->pagibig = $this->PagibigDeduction();
    }
    private function SSSDeduction()
    {
        $sss = SSSContribution::getContribution($this->salary);
        $totalContribution = $sss->employee_contribution + $sss->employer_contribution;
        $totalCompensation = $sss->employee_share + $sss->employer_share;
        $total = $totalCompensation + $totalContribution;
        $total = $this->filters["payroll_type"] === "weekly" ? $total / 4 : $total / 2;
        return [
            "employer_contribution" => $sss->employee_contribution,
            "employee_contribution" =>  $sss->employer_contribution,
            "employer_compensation" => $sss->employee_share,
            "employee_compensation" => $sss->employer_share,
            "total" => $total
        ];
    }
    private function PhilhealthDeduction()
    {
        $philhealth = PhilhealthContribution::getContribution($this->salary);
        $total = 0;
        if ($philhealth->share_type == 'Amount') {
            $total = $philhealth->employee_share + $philhealth->employer_share;
        } else {
            /*      dd($this->salary, $philhealth->employee_share); */
            /*             $employeerCompensation = intdiv($this->salary, $philhealth->employer_share);
            $employeeCompensation = intdiv($this->salary, $philhealth->employee_share); */
            $employeeCompensation = ($this->salary / $philhealth->employer_share) * 100;
            $employeerCompensation = ($this->salary / $philhealth->employer_share) * 100;
            $total = $employeeCompensation + $employeerCompensation;
        }
        $total = $this->filters["payroll_type"] === "weekly" ? $total / 4 : $total / 2;

        return [
            "share_type" => $philhealth->share_type,
            "employer_compensation" => $employeerCompensation,
            "employee_compensation" => $employeeCompensation,
            "total" => $total
        ];
    }
    private function PagibigDeduction()
    {
        /* $pagibig = PagibigContribution::getContribution($this->salary);
        $totalContribution = $sss->employee_contribution + $sss->employer_contribution;
        $totalCompensation = $sss->employee_share + $sss->employer_share;
        $total = $totalCompensation + $totalContribution;
        $total = $this->filters["payroll_type"] === "weekly" ? $total / 4 : $total / 2;
        return [
            "employer_contribution" => $sss->employee_contribution,
            "employee_contribution" =>  $sss->employer_contribution,
            "employer_compensation" => $sss->employee_share,
            "employee_compensation" => $sss->employer_share,
            "total" => $total
        ]; */
    }
}

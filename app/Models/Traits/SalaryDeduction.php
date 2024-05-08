<?php

namespace App\Models\Traits;

use App\Models\PhilhealthContribution;
use App\Models\SSSContribution;

trait SalaryDeduction
{
    public function SSSDeduction($salary, $payrollType)
    {
        $sss = SSSContribution::getContribution($salary);
        $totalContribution = $sss->employee_contribution + $sss->employer_contribution;
        $totalCompensation = $sss->employee_share + $sss->employer_share;
        $total = $totalCompensation + $totalContribution;
        $total = $payrollType === "weekly" ? $total / 4 : $total / 2;
        return [
            "employer_contribution" => $sss->employee_contribution,
            "employee_contribution" =>  $sss->employer_contribution,
            "employer_compensation" => $sss->employee_share,
            "employee_compensation" => $sss->employer_share,
            "total" => $total
        ];
    }

    public function PhilhealthDeduction($salary, $payrollType)
    {
        $philhealth = PhilhealthContribution::getContribution($salary);
        dd($philhealth);
/*         $totalContribution = $sss->employee_contribution + $sss->employer_contribution;
        $totalCompensation = $sss->employee_share + $sss->employer_share;
        $total = $totalCompensation + $totalContribution;
        $total = $payrollType === "weekly" ? $total / 4 : $total / 2;
        return [
            "employer_contribution" => $sss->employee_contribution,
            "employee_contribution" =>  $sss->employer_contribution,
            "employer_compensation" => $sss->employee_share,
            "employee_compensation" => $sss->employer_share,
            "total" => $total
        ]; */
    }
}

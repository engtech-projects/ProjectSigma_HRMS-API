<?php

namespace App\Models\Traits;

use App\Models\PhilhealthContribution;
use App\Models\SSSContribution;

trait SalaryDeduction
{
    public function SSSDeduction($salary, $payrollType)
    {
        $sss = SSSContribution::getContribution($salary);
        $totalContribution = $sss->employee_share + $sss->employer_share;
        $totalCompensation = $sss->employee_compensation + $sss->employer_compensation;
        $totalWisp = $sss->employee_wisp + $sss->employer_wisp;
        $total = $totalCompensation + $totalContribution + $totalWisp;
        $total = $payrollType === "weekly" ? $total / 4 : $total / 2;
        return [
            "employee_contribution" => $sss->employee_share,
            "employer_contribution" =>  $sss->employer_share,
            "employee_compensation" => $sss->employee_compensation,
            "employer_compensation" => $sss->employer_compensation,
            "employee_wisp" => $sss->employee_wisp,
            "employer_wisp" => $sss->employer_wisp,
            "total" => $total,
        ];
    }

    public function PhilhealthDeduction($salary, $payrollType)
    {
        $philhealth = PhilhealthContribution::getContribution($salary);
        dd($philhealth);
    }
}

<?php

namespace App\Models\Traits;

use App\Enums\RequestStatusType;
use App\Models\CashAdvance;
use App\Models\PhilhealthContribution;
use App\Models\SalaryGradeStep;
use App\Models\SSSContribution;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait EmployeePayroll
{
    public function cash_advance_payroll(): HasMany
    {
        return $this->hasMany(CashAdvance::class)->where('request_status', RequestStatusType::APPROVED->value);
    }
    public function salary_grade_payroll(): HasOne
    {
        return $this->hasOne(SalaryGradeStep::class);
    }

    public function salary_gross_pay($dtr)
    {
        $salaryGrade = $this->current_employment?->employee_salarygrade;
        $dailyRate = $salaryGrade?->dailyRate ?: 0;
        $result = [];
        foreach ($dtr as $key => $value) {
            $result[$key]["reg_hrs"] = $value["reg_hrs"] * $dailyRate;
            $result[$key]["overtime"] = $value["overtime"] * $dailyRate;
        }
        return $result;
    }

    public function salary_deduction($filters)
    {
        $salaryGrade = $this->current_employment?->employee_salarygrade;
        $salary = $salaryGrade->monthly_salary_amount;
        $sss = $this->sss_deduction($salary, $filters);
        $philhealth = $this->philhealth_deduction($salary, $filters);
        return [
            "cash_advance" => "", //$salaryDeduction->cashAdvance->cashAdvance,
            "sss" => $sss, //$filters["deduct_sss"] ? $salaryDeduction->sss : [],
            "phic" => $philhealth, // $filters["deduct_philhealth"] ? $salaryDeduction->philhealth : [],
            "hmdf" => 0, //$filters["deduct_pagibig"] ? $salaryDeduction->pagibig : [],
            "ewtc" => 0,  //$salaryDeduction->withHoldingTax,
            "loan" => 0, //$salaryDeduction->loan->loan
        ];
    }

    public function sss_deduction($salary, $filters)
    {
        $deduction = new SSSContribution();
        $contribution =  $deduction->contribution($salary);
        $compensation = $deduction->compensation($salary);

        if ($filters["payroll_type"] === "monthly") {
            $contribution["employee"] = $contribution["employee"] / 4;
            $contribution["employer"] = $contribution["employer"] / 4;
        } else {
            $contribution["employee"] = $contribution["employee"] / 2;
            $contribution["employer"] = $contribution["employer"] / 2;
        }

        return [
            "contribution" => $contribution,
            "compensation" => $compensation
        ];
    }
    public function philhealth_deduction($salary)
    {
        $deduction = new PhilhealthContribution();
    }
}

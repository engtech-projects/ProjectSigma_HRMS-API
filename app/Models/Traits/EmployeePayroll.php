<?php

namespace App\Models\Traits;

use App\Enums\RequestStatusType;
use App\Models\CashAdvance;
use App\Models\PagibigContribution;
use App\Models\PhilhealthContribution;
use App\Models\SalaryGradeStep;
use App\Models\SSSContribution;
use App\Models\WitholdingTaxContribution;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

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
            $result[$key]["reg_hrs"] = round($value["reg_hrs"] / 8 * $dailyRate, 2);
            $result[$key]["overtime"] = round($value["overtime"] / 8 * $dailyRate, 2);
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

    public function sss_deduction($salary, $type)
    {
        $deduction = new SSSContribution();
        $sss =  $deduction->contribution($salary);
        $result = [
            "employer_contribution" => 0,
            "employee_contribution" =>  0,
            "employer_compensation" => 0,
            "employee_compensation" => 0,
            "total_contribution" => 0,
            "total_compensation" => 0,
        ];

        if ($sss) {
            $contribution = $this->getTotal([
                "employer" => $sss->employer_contribution,
                "employee" => $sss->employee_contribution
            ], $type);
            $compensation = $this->getTotal([
                "employer" => $sss->employer_share,
                "employee" => $sss->employee_share
            ], $type);

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

    private function getTotal($compensation, $payrollType)
    {
        if ($compensation) {
            if ($payrollType == "weekly") {
                $compensation["employee"] =  round($compensation["employee"] / 4, 2);
                $compensation["employer"] =  round($compensation["employer"] / 4, 2);
            } else {
                $compensation["employee"] =  round($compensation["employee"] / 2, 2);
                $compensation["employer"] =  round($compensation["employer"] / 2, 2);
            }
        }
        return $compensation;
    }
    public function philhealth_deduction($salary, $payrollType)
    {
        $deduction = new PhilhealthContribution();
        $philhealth = $deduction->contribution($salary);
        $result = [
            "share_type" => 0,
            "employer_compensation" => 0,
            "employee_compensation" => 0,
            "total_compensation" => 0,
        ];
        if ($philhealth) {
            if ($deduction->share_type == 'Amount') {
                $employeeCompensation = $philhealth->employee_share;
                $employeerCompensation = $philhealth->employer_share;
            } else {
                $employeeCompensation = round(($philhealth->employee_share / 100) * $salary, 2);
                $employeerCompensation = round(($philhealth->employer_share / 100) * $salary, 2);
            }
            $compensation = $this->getTotal([
                "employer" => $employeerCompensation,
                "employee" => $employeeCompensation
            ], $payrollType);
            $result = [
                "share_type" => $philhealth->share_type,
                "employer_compensation" => $compensation["employer"],
                "employee_compensation" => $compensation["employee"],
                "total_compensation" => $compensation["employer"] + $compensation["employee"]
            ];
        }
        return $result;
    }

    public function pagibig_deduction($salary, $payrollType)
    {
        $deduction = new PagibigContribution();
        $pagibig = $deduction->contribution($salary);
        $result = [
            "employer_compensation" => 0,
            "employee_compensation" => 0,
            "total_compensation" => 0,
        ];
        if ($pagibig) {
            $employeeCompensation = round(($pagibig->employee_share_percent / 100) * $salary, 2);
            $employeerCompensation = round(($pagibig->employer_share_percent / 100) * $salary, 2);

            $compensation = $this->getTotal([
                "employer" => $employeerCompensation,
                "employee" => $employeeCompensation
            ], $payrollType);
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

    public function with_holding_tax_deduction($salary)
    {
        $deduction = new WitholdingTaxContribution();
        $wht = $deduction->contribution($salary);
        $total = 0;
        if ($wht) {
            $taxBase = $wht->tax_base;
            $taxAmount = $wht->tax_amount;
            $diff = abs($taxBase - $salary);
            $total = round(($wht->tax_percent_over_base_decimal) * $diff + $taxAmount, 2);
        }
        return $total;
    }

    public function loan_deduction($salary, $type, $date)
    {
        $deduction = 0;
        $date = Carbon::parse($date);
        $loan = $this->employee_loan->first();
        if ($loan) {
            if (!$loan->loanPaid()) {
                if ($loan->deduction_date_start->lt($date)) {
                    $deduction = $loan->installment_deduction;
                }
                if ($type === "weekly") {
                    $deduction = $deduction / 4;
                } else {
                    $deduction = $deduction / 2;
                }
            }
        }
        return $deduction;
    }
    public function cash_advance_deduction($salary, $type, $date)
    {
        $deduction = 0;
        $date = Carbon::parse($date);
        $cashAdvance = $this->cash_advance()->requestStatusApproved()->first();

        if ($cashAdvance) {
            if (!$cashAdvance->cashPaid())
                if ($cashAdvance->deduction_date_start->lt($date)) {
                    $deduction = $cashAdvance->installment_deduction;
                }
            if ($type === "weekly") {
                $deduction = $deduction / 4;
            } else {
                $deduction = $deduction / 2;
            }
        }
        return $deduction;
    }
}

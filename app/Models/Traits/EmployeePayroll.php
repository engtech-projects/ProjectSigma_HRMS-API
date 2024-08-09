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
        return [
            "regular" => [
                "reg_hrs" =>  round($dtr["regular"]["reg_hrs"] / 8 * $dailyRate, 2),
                "overtime" => round($dtr["regular"]["overtime"] / 8 * $dailyRate * 1.25, 2),
            ],
            "rest" => [
                "reg_hrs" => round($dtr["rest"]["reg_hrs"] / 8 * $dailyRate * 1.3, 2),
                "overtime" => round($dtr["rest"]["overtime"] / 8 * $dailyRate * 1.6, 2),
            ],
            "regular_holidays" => [
                "reg_hrs" => round($dtr["regular_holidays"]["reg_hrs"] / 8 * $dailyRate, 2),
                "overtime" => round($dtr["regular_holidays"]["overtime"] / 8 * $dailyRate * 1.6, 2),
            ],
            "special_holidays" => [
                "reg_hrs" => round($dtr["special_holidays"]["reg_hrs"] / 8 * $dailyRate * 1.3, 2),
                "overtime" => round($dtr["special_holidays"]["overtime"] / 8 * $dailyRate, 2),
            ],
        ];
    }

    public function salary_charge_pay($dtr, $getId)
    {
        $salaryGrade = $this->current_employment?->employee_salarygrade;
        $dailyRate = $salaryGrade?->dailyRate ?: 0;
        $travelcharge = collect();
        $special_holidaycharge = collect();
        $leavecharge = collect();
        $projects = collect();
        $departments = collect();
        foreach ($dtr["departments"] as $key => $value) {
            if(count($dtr["departments"]) > 0) {
                $departments->push([
                    "id" => $value["id"],
                    "amount" => round($value["reg_hrs"] / 8 * $dailyRate, 2),
                    "amount_overtime" => round($value["overtime"] / 8 * 1.25 * $dailyRate, 2),
                    "amount_regular_holidays" => round($value["regular_holidays_hrs"] / 8 * 1 * $dailyRate, 2),
                    "amount_regular_ot_holidays" => round($value["regular_holidays_hrs"] / 8 * 1.6 * $dailyRate, 2),
                ]);
            }
        }

        foreach ($dtr["special_holiday"] as $key => $value) {
            if(count($dtr["special_holiday"]) > 0) {
                $special_holidaycharge->push([
                    "id" => $getId,
                    "amount" => round($value["reg_hrs"] / 8 * 1.3 * $dailyRate, 2),
                ]);
            }
        }
        foreach ($dtr["travels"] as $key => $value) {
            if(count($dtr["travels"]) > 0) {
                $travelcharge->push([
                    "id" => $getId,
                    "amount" => round($value["reg_hrs"] / 8 * $dailyRate, 2),
                ]);
            }
        }
        foreach ($dtr["leaves"] as $key => $value) {
            if(count($dtr["leaves"]) > 0) {
                $leavecharge->push([
                    "id" => $getId,
                    "amount" => round($value["reg_hrs"] / 8 * $dailyRate, 2),
                ]);
            }
        }
        foreach ($dtr["projects"] as $key => $value) {
            if(count($dtr["projects"]) > 0) {
                $projects->push([
                    "id" => $value["id"],
                    "amount" => round($value["reg_hrs"] / 8 * $dailyRate, 2),
                    "amount_overtime" => round($value["overtime"] / 8 * 1.25 * $dailyRate, 2),
                    "amount_regular_holidays" => round($value["regular_holidays_hrs"] / 8 * 1 * $dailyRate, 2),
                    "amount_regular_ot_holidays" => round($value["regular_holidays_hrs"] / 8 * 1.6 * $dailyRate, 2),
                ]);
            }
        }
        $deduction = new SSSContribution();
        $sss =  $deduction->contribution($salaryGrade?->monthly_salary_amount);
        $deduction = new PhilhealthContribution();
        $philhealth = $deduction->contribution($salaryGrade?->monthly_salary_amount);
        $deduction = new PagibigContribution();
        $pagibig = $deduction->contribution($salaryGrade?->monthly_salary_amount);
        $result = [
            "travels" => $travelcharge,
            "leaves" => $leavecharge,
            "projects" => $projects,
            "departments" => $departments,
            "special_holiday" => $special_holidaycharge,
            "sss" => $sss,
            "philhealth" => $philhealth,
            "pagibig" => $pagibig,
        ];
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
            $result = [
                "employer_contribution" => $sss->employer_contribution,
                "employee_contribution" =>  $sss->employee_contribution,
                "employer_compensation" => $sss->employer_share,
                "employee_compensation" => $sss->employee_share,
                "total_contribution" => $sss->employer_contribution + $sss->employee_contribution,
                "total_compensation" => $sss->employer_share + $sss->employee_share
            ];
        }

        return $result;
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
            if ($philhealth->share_type == 'Amount') {
                $employeeCompensation = $philhealth->employee_share;
                $employerCompensation = $philhealth->employer_share;
            } else {
                $employeeCompensation = round(($philhealth->employee_share / 100) * $salary, 2);
                $employerCompensation = round(($philhealth->employer_share / 100) * $salary, 2);
            }
            $result = [
                "share_type" => $philhealth->share_type,
                "employer_compensation" => $employerCompensation,
                "employee_compensation" => $employeeCompensation,
                "total_compensation" => $employeeCompensation + $employerCompensation,
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
            $employerCompensation = round(($pagibig->employer_share_percent / 100) * $salary, 2);

            $result = [
                "employer_compensation" => $employerCompensation > $pagibig->employer_maximum_contribution ?
                    $pagibig->employer_maximum_contribution : $employerCompensation,
                "employee_compensation" => $employeeCompensation > $pagibig->employee_maximum_contribution ?
                    $pagibig->employee_maximum_contribution : $employeeCompensation,
                "total_compensation" => $employerCompensation + $employeeCompensation
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
        $date = Carbon::parse($date);
        $loans = $this->employee_loan()->get();
        $loans = $loans->filter(function ($loan) use ($date) {
            return !$loan->loanPaid() && $loan->deduction_date_start->lt($date);
        });
        $loans = $loans->map(function ($loan) {
            return [
                ...collect($loan),
                "max_payroll_payment" => $loan->max_payroll_payment,
            ];
        });
        $totalPaid = $loans->sum("max_payroll_payment");
        return [
            "total_paid" => $totalPaid,
            "loans" => $loans,
        ];
    }

    public function cash_advance_deduction($salary, $type, $date)
    {
        $date = Carbon::parse($date);
        $cashAdvance = $this->cash_advance()->requestStatusApproved()->get();
        $cashAdvance->filter(function ($loan) use ($date) {
            return !$loan->cashPaid() && $loan->deduction_date_start->lt($date);
        });
        $cashAdvance->map(function ($loan) {
            return [
                ...collect($loan),
                "max_payable" => $loan->max_payroll_payment,
            ];
        });
        $totalPaid = $cashAdvance->sum("max_payroll_payment");
        return [
            "total_paid" => $totalPaid,
            "cash_advance" => $cashAdvance,
        ];
    }

    public function other_deductions($salary, $type, $date)
    {
        $date = Carbon::parse($date);
        $otherDeduction = $this->other_deduction()->get();
        $otherDeduction->filter(function ($loan) use ($date) {
            return !$loan->cashPaid() && $loan->deduction_date_start->lt($date);
        });
        $otherDeduction->map(function ($loan) {
            return [
                ...collect($loan),
                "max_payable" => $loan->max_payroll_payment,
            ];
        });
        $totalPaid = $otherDeduction->sum("max_payroll_payment");
        return [
            "total_paid" => $totalPaid,
            "other_deduction" => $otherDeduction,
        ];
    }
}

<?php

namespace App\Models\Traits;

use App\Enums\RequestStatusType;
use App\Http\Services\Payroll\PayrollService;
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
                "reg_hrs" =>  PayrollService::getSalaryByRateHour("regular", "reg_hrs", $dailyRate, $dtr["regular"]["reg_hrs"]),
                "overtime" => PayrollService::getSalaryByRateHour("regular", "overtime", $dailyRate, $dtr["regular"]["overtime"]),
            ],
            "rest" => [
                "reg_hrs" =>  PayrollService::getSalaryByRateHour("rest", "reg_hrs", $dailyRate, $dtr["rest"]["reg_hrs"]),
                "overtime" => PayrollService::getSalaryByRateHour("rest", "overtime", $dailyRate, $dtr["rest"]["overtime"]),
            ],
            "regular_holidays" => [
                "reg_hrs" =>  PayrollService::getSalaryByRateHour("regular_holidays", "reg_hrs", $dailyRate, $dtr["regular_holidays"]["reg_hrs"]),
                "overtime" => PayrollService::getSalaryByRateHour("regular_holidays", "overtime", $dailyRate, $dtr["regular_holidays"]["overtime"]),
            ],
            "special_holidays" => [
                "reg_hrs" =>  PayrollService::getSalaryByRateHour("special_holidays", "reg_hrs", $dailyRate, $dtr["special_holidays"]["reg_hrs"]),
                "overtime" => PayrollService::getSalaryByRateHour("special_holidays", "overtime", $dailyRate, $dtr["special_holidays"]["overtime"]),
            ],
        ];
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
            "employee_contribution" => 0,
            "employer_compensation" => 0,
            "employee_compensation" => 0,
            "employer_wisp" => 0,
            "employee_wisp" => 0,
            "total_contribution" => 0,
            "total_compensation" => 0,
            "total_wisp" => 0,
        ];

        if ($sss) {
            $result = [
                "employer_contribution" => $sss->employer_share,
                "employee_contribution" =>  $sss->employee_share,
                "employer_compensation" => $sss->employer_compensation,
                "employee_compensation" => $sss->employee_compensation,
                "employer_wisp" => $sss->employer_wisp,
                "employee_wisp" => $sss->employee_wisp,
                "total_contribution" => $sss->employer_share + $sss->employee_share,
                "total_compensation" => $sss->employer_compensation + $sss->employee_compensation,
                "total_wisp" => $sss->employer_wisp + $sss->employee_wisp,
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
            "employer_contribution" => 0,
            "employee_contribution" => 0,
            "total_contribution" => 0,
        ];
        if ($philhealth) {
            if ($philhealth->share_type == 'Amount') {
                $employeeContribution = $philhealth->employee_share;
                $employerContribution = $philhealth->employer_share;
            } else {
                $employeeContribution = round(($philhealth->employee_share / 100) * $salary, 2);
                $employerContribution = round(($philhealth->employer_share / 100) * $salary, 2);
            }
            $result = [
                "share_type" => $philhealth->share_type,
                "employer_contribution" => $employerContribution,
                "employee_contribution" => $employeeContribution,
                "total_contribution" => $employeeContribution + $employerContribution,
            ];
        }
        return $result;
    }

    public function pagibig_deduction($salary, $payrollType)
    {
        $deduction = new PagibigContribution();
        $pagibig = $deduction->contribution($salary);
        $result = [
            "employer_contribution" => 0,
            "employee_contribution" => 0,
            "total_contribution" => 0,
        ];
        if ($pagibig) {
            $employeeContribution = round(($pagibig->employee_share_percent / 100) * $salary, 2);
            $employerContribution = round(($pagibig->employer_share_percent / 100) * $salary, 2);

            $result = [
                "employer_contribution" => $employerContribution > $pagibig->employer_maximum_contribution ?
                    $pagibig->employer_maximum_contribution : $employerContribution,
                "employee_contribution" => $employeeContribution > $pagibig->employee_maximum_contribution ?
                    $pagibig->employee_maximum_contribution : $employeeContribution,
                "total_contribution" => $employerContribution + $employeeContribution
            ];
        }
        return $result;
    }

    public function with_holding_tax_deduction($salary, $payrollType)
    {
        $deduction = new WitholdingTaxContribution();
        $wht = $deduction->contribution($salary);
        $total = 0;
        if ($wht) {
            $taxBase = $wht->tax_base;
            $taxAmount = $wht->tax_amount;
            $excess = $salary - $taxBase ?? 0;
            $excessTaxAmount = $excess * $wht->percent_over_base_decimal;
            $total = round($taxAmount + $excessTaxAmount, 2);
        }
        return PayrollService::getPayrollTypeValue($payrollType, $total);
    }

    public function loan_deduction($salary, $type, $date)
    {
        $date = Carbon::parse($date);
        $loans = $this->employee_loan()->get();
        $loans = collect($loans->filter(function ($loan) use ($date) {
            return $loan->balance > 0 && $loan->deduction_date_start->lte($date);
        })->values()->all());
        $loans = $loans->map(function ($loan) use ($type) {
            $deduction = floatval(Payrollservice::getPayrollTypeValue($type, $loan->installment_deduction));
            if ($deduction > $loan->balance) {
                $deduction = $loan->balance;
            }
            return [
                ...collect($loan),
                "max_payroll_payment" => $deduction,
            ];
        });
        $totalPaid = $loans->sum("max_payroll_payment");
        return [
            "total_paid" => $totalPaid,
            "loans" => $loans->values()->all(),
        ];
    }

    public function cash_advance_deduction($salary, $type, $date)
    {
        $date = Carbon::parse($date);
        $cashAdvance = $this->cash_advance()->requestStatusApproved()->get();
        $cashAdvance = collect($cashAdvance->filter(function ($cAdv) use ($date) {
            return $cAdv->balance > 0 && $cAdv->deduction_date_start->lte($date);
        })->values()->all());
        $cashAdvance = $cashAdvance->map(function ($cAdv) use ($type) {
            $deduction = floatval(Payrollservice::getPayrollTypeValue($type, $cAdv->installment_deduction));
            if ($deduction > $cAdv->balance) {
                $deduction = $cAdv->balance;
            }
            return [
                ...collect($cAdv),
                "max_payroll_payment" => $deduction,
            ];
        });
        $totalPaid = $cashAdvance->sum("max_payroll_payment");
        return [
            "total_paid" => $totalPaid,
            "cash_advance" => $cashAdvance->values()->all(),
        ];
    }

    public function other_deductions($salary, $type, $date)
    {
        $date = Carbon::parse($date);
        $otherDeduction = $this->other_deduction()->get();
        $otherDeduction = collect($otherDeduction->filter(function ($oDed) use ($date) {
            return $oDed->balance > 0 && $oDed->deduction_date_start->lte($date);
        })->values()->all());
        $otherDeduction = $otherDeduction->map(function ($oDed) use ($type) {
            $deduction = floatval(Payrollservice::getPayrollTypeValue($type, $oDed->installment_deduction));
            if ($deduction > $oDed->balance) {
                $deduction = $oDed->balance;
            }
            return [
                ...collect($oDed),
                "max_payroll_payment" => $deduction,
            ];
        });
        $totalPaid = $otherDeduction->sum("max_payroll_payment");
        return [
            "total_paid" => $totalPaid,
            "other_deduction" => $otherDeduction->values()->all(),
        ];
    }
}

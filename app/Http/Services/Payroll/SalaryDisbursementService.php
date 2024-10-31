<?php

namespace App\Http\Services\Payroll;

use App\Enums\RequestStatuses;
use App\Models\PayrollDetail;
use App\Models\PayrollDetailsCharging;
use App\Models\PayrollRecord;

class SalaryDisbursementService
{
    public static function getPayrollRecordsForDisbursement($payrollDate, $payrollType, $releaseType)
    {
        return PayrollRecord::where([
            "payroll_date" => $payrollDate,
            "payroll_type" => $payrollType,
            "release_type" => $releaseType,
        ])
        ->whereNot("request_status", RequestStatuses::DENIED)
        ->get();
    }
    public static function getPayrollDetails($payrollIds)
    {

        return PayrollDetail::whereIn("payroll_record_id", $payrollIds)
        ->with(['payroll_record', 'otherDeductionPayments.deduction.otherdeduction', 'loanPayments.deduction.loan'])
        ->orderBy("created_at", "DESC")
        ->get()
        ->append([
            'total_basic_pays',
            'total_overtime_pays',
            'total_cash_advance_payments',
            'total_loan_payments',
            'total_other_deduction_payments',
        ]);
    }
    public static function formatSalaries($groupedSalaryChargings)
    {
        $returnData = [];
        foreach ($groupedSalaryChargings as $key => $dataCollection) {
            $basic_pay_names = [
                "Salary Regular Regular",
                "Salary Rest Regular",
                "Salary RegularHoliday Regular",
                "Salary SpecialHoliday Regular",
                "Salary Adjustment",
            ];
            $overtime_pay_names = [
                "Salary Regular Overtime",
                "Salary Rest Overtime",
                "Salary RegularHoliday Overtime",
                "Salary SpecialHoliday Overtime",
            ];
            $basicSalaries = $dataCollection->filter(function ($chargingItem) use ($basic_pay_names) {
                return in_array($chargingItem['name'], $basic_pay_names);
            });
            $overtimeSalaries = $dataCollection->filter(function ($chargingItem) use ($overtime_pay_names) {
                return in_array($chargingItem['name'], $overtime_pay_names);
            });
            $payBasic = round($basicSalaries->sum("amount"), 2);
            $payOvertime = round($overtimeSalaries->sum("amount"), 2);
            $payGross = round($payBasic + $payOvertime, 2);

            $returnData[$key] = [
                "data" => $dataCollection,
                "summary" => [
                    // SALARY
                    "charging_pay_basic" => $payBasic, // TOTAL OF REGULAR PAYS, ADJUSMENTS
                    "charging_pay_overtime" => $payOvertime, // TOTAL OF OVERTIME PAYS
                    "charging_pay_gross" => $payGross,
                ]
            ];
        }
        return $returnData;
    }
    public static function formatSummary($groupedPayrollDetails)
    {
        $returnData = [];
        foreach ($groupedPayrollDetails as $key => $dataCollection) {
            $returnData[$key] = [
                "data" => $dataCollection,
                "summary" => [
                    "no_of_employee" => $dataCollection->count(),
                    // SALARY
                    "pay_basic" => $dataCollection->sum("total_basic_pays"), // TOTAL OF REGULAR PAYS, ADJUSMENTS
                    "pay_overtime" => $dataCollection->sum("total_overtime_pays"), // TOTAL OF OVERTIME PAYS
                    "pay_gross" => $dataCollection->sum("gross_pay"),
                    // DEDUCTIONS
                    "deduct_sss_employee_contribution" => $dataCollection->sum("sss_employee_contribution"),
                    "deduct_sss_employee_compensation" => $dataCollection->sum("sss_employee_compensation"),
                    "deduct_sss_employee_wisp" => $dataCollection->sum("sss_employee_wisp"),
                    "deduct_phihealth_employee_cotribution" => $dataCollection->sum("phihealth_employee_cotribution"),
                    "deduct_pagibig_employee_cotribution" => $dataCollection->sum("pagibig_employee_cotribution"),
                    "deduct_withholdingtax" => $dataCollection->sum("withholdingtax_contribution"),
                    "deduct_cashadvance" => $dataCollection->sum("total_cash_advance_payments"),
                    "deduct_loan" => $dataCollection->sum("total_loan_payments"),
                    "deduct_otherdeduction" => $dataCollection->sum("total_other_deduction_payments"),
                    "deduct_total" => $dataCollection->sum("total_deduct"),
                    // NET
                    "net_pay" => $dataCollection->sum("net_pay"),
                ]
            ];
        }
        return $returnData;
    }
    public static function getPayrollSummary($payrollRecordIds)
    {
        $payrollDetails = SalaryDisbursementService::getPayrollDetails($payrollRecordIds);
        $payrollDetailsIds = $payrollDetails->pluck("id");
        $uniqueGroup =  $payrollDetails->groupBy('payroll_record.charging_name');
        $chargings = PayrollDetailsCharging::whereIn("payroll_details_id", $payrollDetailsIds)->get()->append(["charging_name"]);
        $uniqueSalaries = $chargings->groupBy(['charging_name']);
        $formattedSalaries = SalaryDisbursementService::formatSalaries($uniqueSalaries);
        $newUniqueGroup = [];
        foreach ($formattedSalaries as $key => $value) {
            $newUniqueGroup[$key]["details"] = $uniqueGroup[$key] ?? [];
            $newUniqueGroup[$key]["salaries"] = $value["summary"];
        }
        return $newUniqueGroup;
    }
}

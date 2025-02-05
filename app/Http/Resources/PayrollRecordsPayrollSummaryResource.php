<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class PayrollRecordsPayrollSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $dataCollection = collect($this["details"]);
        $salaries = $this["salaries"];
        $totalDeduct = $dataCollection->sum("total_deduct");
        return [
            "data" => parent::toArray($request),
            "summary" => [
                "no_of_employee" => $dataCollection->count(),
                "charging_type_name" => $salaries["charge_type"],
                // SALARY
                "pay_basic" => $dataCollection->sum("total_basic_pays"), // TOTAL OF REGULAR PAYS, ADJUSTMENTS
                "pay_overtime" => $dataCollection->sum("total_overtime_pays"), // TOTAL OF OVERTIME PAYS
                "pay_gross" => $dataCollection->sum("gross_pay"),
                "charging_pay_basic" => $salaries["charging_pay_basic"], // TOTAL OF REGULAR PAYS, ADJUSTMENTS
                "charging_pay_overtime" => $salaries["charging_pay_overtime"], // TOTAL OF OVERTIME PAYS
                "charging_pay_gross" => $salaries["charging_pay_gross"],
                // DEDUCTIONS
                "deduct_sss_employee_contribution" => $dataCollection->sum("sss_employee_contribution"),
                "deduct_sss_employee_compensation" => $dataCollection->sum("sss_employee_compensation"),
                "deduct_sss_employee_wisp" => $dataCollection->sum("sss_employee_wisp"),
                "deduct_philhealth_employee_contribution" => $dataCollection->sum("philhealth_employee_contribution"),
                "deduct_pagibig_employee_contribution" => $dataCollection->sum("pagibig_employee_contribution"),
                "deduct_withholdingtax" => $dataCollection->sum("withholdingtax_contribution"),
                "deduct_cashadvance" => $dataCollection->sum("total_cash_advance_payments"),
                "deduct_loan" => $dataCollection->sum("total_loan_payments"),
                "deduct_otherdeduction" => $dataCollection->sum("total_other_deduction_payments"),
                "deduct_total" => $totalDeduct,
                // NET
                "net_pay" => round($dataCollection->sum("gross_pay") - $totalDeduct, 2),
                "charging_net_pay" => round($salaries["charging_pay_gross"] - $totalDeduct, 2),
            ]
        ];
    }
}

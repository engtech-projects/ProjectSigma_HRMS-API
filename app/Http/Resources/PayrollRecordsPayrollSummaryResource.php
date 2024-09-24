<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PayrollRecordsPayrollSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $dataCollection = collect(parent::toArray($request));
        return [
            "data" => parent::toArray($request),
            "summary" => [
                "no_of_employee" => $dataCollection->count(),
                // SALARY
                "pay_basic" => $dataCollection->sum("total_basic_pays"), // TOTAL OF REGULAR PAYS, ADJUSMENTS
                "pay_overtime" => $dataCollection->sum("total_overtime_pays"), // TOTAL OF OVERTIME PAYS
                "pay_gross" => $dataCollection->sum("gross_pay"),
                // DEDUCTIONS
                "deduct_sss_employee_contribution" => $dataCollection->sum("sss_employee_contribution"),
                "deduct_sss_employee_compensation" => $dataCollection->sum("sss_employee_compensation"),
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
}

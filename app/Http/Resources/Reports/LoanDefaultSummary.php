<?php

namespace App\Http\Resources\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanDefaultSummary extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $dataCollection = collect(parent::toArray($request))
        ->groupBy("employee_id")
        ->map(function ($employeeData) {
            return [
                ...$employeeData->first(),
                "employee_pagibig_no" => $employeeData->first()->employee->company_employments->pagibig_number,
                "first_name" => $employeeData->first()->employee->first_name,
                "middle_name" => $employeeData->first()->employee->middle_name,
                "last_name" => $employeeData->first()->employee->family_name,
                "suffix_name" => $employeeData->first()->employee->suffix_name,
                "fullname" => $employeeData->first()->employee->fullname_first,
                "loan_type" => "loan_type",
                "percov" => $employeeData->first()->percov,
                "total_payments" => $employeeData->first()->loanPayments->sum("amount"),
                "payroll_record" => [
                    ...$employeeData->first()->payroll_record->toArray(),
                    "charging_name" => $employeeData->first()->payroll_record->charging_name,
                ],
            ];
        });
        return [
            "data" => parent::toArray($request),
            "summary" => [
                "no_of_employee" => $dataCollection->count(),
                "overall_total_payments" => $dataCollection->sum("total_payments"),
            ]
        ];
    }
}

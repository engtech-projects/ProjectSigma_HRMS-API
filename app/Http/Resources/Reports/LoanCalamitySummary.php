<?php

namespace App\Http\Resources\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanCalamitySummary extends JsonResource
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
                "first_name" => $employeeData->first()['employee']['first_name'],
                "middle_name" => $employeeData->first()['employee']['middle_name'],
                "last_name" => $employeeData->first()['employee']['family_name'],
                "suffix" => $employeeData->first()['employee']['name_suffix'],
                "fullname" => $employeeData->first()['employee']['fullname_first'],
                "loan_type" => $employeeData->first()['loan_type'],
                "total_payments" => $employeeData->sum("total_payments"),
            ];
        });
        return [
            "data" => $dataCollection->values(),
            "summary" => [
                "no_of_employee" => $dataCollection->count(),
                "overall_total_payments" => $dataCollection->sum("total_payments"),
            ]
        ];
    }
}

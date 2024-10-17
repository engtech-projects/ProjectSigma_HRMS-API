<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SssRemittanceSummaryResource extends JsonResource
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
                'sss_employer_contribution' => $employeeData->sum("sss_employer_contribution"),
                'sss_employee_contribution' => $employeeData->sum("sss_employee_contribution"),
                'sss_employer_compensation' => $employeeData->sum("sss_employer_compensation"),
                'sss_employee_compensation' => $employeeData->sum("sss_employee_compensation"),
                'sss_employer_wisp' => $employeeData->sum("sss_employer_wisp"),
                'sss_employee_wisp' => $employeeData->sum("sss_employee_wisp"),
                'total_sss_contribution' => $employeeData->sum("total_sss_contribution"),
                'total_sss_compensation' => $employeeData->sum("total_sss_compensation"),
                'total_sss_wisp' => $employeeData->sum("total_sss_wisp"),
                'total_sss' => $employeeData->sum("total_sss"),
            ];
        });
        return [
            "data" => parent::toArray($request),
            "summary" => [
                "no_of_employee" => $dataCollection->count(),
                "total_employer_contribution" => $dataCollection->sum("sss_employer_contribution"),
                "total_employee_contribution" => $dataCollection->sum("sss_employee_contribution"),
                "total_contribution" => $dataCollection->sum("total_sss_contribution"),
                "total_employee_compensation" => $dataCollection->sum("sss_employee_compensation"),
                "total_employer_compensation" => $dataCollection->sum("sss_employer_compensation"),
                "total_compensation" => $dataCollection->sum("total_sss_compensation"),
                "total_sss" => $dataCollection->sum("total_sss"),
            ]
        ];
    }
}

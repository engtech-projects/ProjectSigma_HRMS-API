<?php

namespace App\Http\Resources\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OtherDeductionDefaultSummary extends JsonResource
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
                'total_payments' => $employeeData->sum("total_payments"),
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

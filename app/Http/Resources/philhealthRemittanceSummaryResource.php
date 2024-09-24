<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class philhealthRemittanceSummaryResource extends JsonResource
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
                "total_employer_contribution" => $dataCollection->sum("philhealth_employer_contribution"),
                "total_employee_contribution" => $dataCollection->sum("philhealth_employee_contribution"),
                "total_contribution" => $dataCollection->sum("total_philhealth_contribution"),
                "total_employee_compensation" => $dataCollection->sum("philhealth_employee_compensation"),
                "total_employer_compensation" => $dataCollection->sum("philhealth_employer_compensation"),
                "total_compensation" => $dataCollection->sum("total_philhealth_compensation"),
                "total_philhealth" => $dataCollection->sum("total_philhealth"),
            ]
        ];
    }
}

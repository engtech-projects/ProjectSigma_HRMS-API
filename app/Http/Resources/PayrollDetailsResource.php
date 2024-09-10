<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PayrollDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            ...parent::toArray($request),
            "employee" => new EmployeeSummaryResource($this->employee),
            "adjustments" => $this->adjustments,
            "deductions" => $this->deductions,
            "charges" => $this->charges,
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SssGroupRemittanceResource extends JsonResource
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
            "employee_name" => $this->employee->fullname_first,
            "employee_sss_id" => $this->employee->company_employments->sss_number,
            "total_contribution" => $this->total_sss_contribution,
            "total_compensation" => $this->total_sss_compensation,
            "total_sss" => $this->total_sss,
        ];
    }
}

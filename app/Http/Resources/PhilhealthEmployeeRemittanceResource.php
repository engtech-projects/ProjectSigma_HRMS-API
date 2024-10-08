<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PhilhealthEmployeeRemittanceResource extends JsonResource
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
            "employee_name" => $this['employee']['fullname_last'],
            "employee_philhealth_id" => $this['employee']['company_employments']['phic_number'],
            "total_contribution" => $this['total_philhealth_contribution'],
        ];
    }
}

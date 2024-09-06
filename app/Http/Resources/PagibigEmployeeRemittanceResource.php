<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PagibigEmployeeRemittanceResource extends JsonResource
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
            "employee_pagibig_id" => $this->employee->company_employments->pagibig_number,
            "total_contribution" => $this->total_pagibig_contribution,
            "total_compensation" => $this->total_pagibig_compensation,
            "total_pagibig" => $this->total_pagibig,
        ];
    }
}

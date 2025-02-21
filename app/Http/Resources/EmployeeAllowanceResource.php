<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeAllowanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "employee_id" => $this->pivot->employee_id,
            "employee" => $this->fullname_first,
            "employee_position" => $this->current_position_name,
            "allowance_rate" => $this->pivot->allowance_rate,
            "allowance_rate_formatted" => number_format($this->pivot->allowance_rate, 2),
            "allowance_days" => $this->pivot->allowance_days,
            "allowance_amount" => $this->pivot->allowance_amount,
            "allowance_amount_formatted" => number_format($this->pivot->allowance_amount, 2),
        ];
    }
}

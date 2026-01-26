<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AllowanceRecordsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "allowance_amount" => $this->allowance_amount,
            "allowance_amount_formatted" => number_format($this->allowance_amount, 2),
            "allowance_days" => $this->allowance_days,
            "allowance_rate" => $this->allowance_rate,
            "allowance_rate_formatted" => number_format($this->allowance_rate, 2),
            "allowance_request" => $this->allowance_request,
            "employee" => [
                "id" => $this->employee->id,
                "fullname_first" => $this->employee->fullname_first,
                "fullname_last" => $this->employee->fullname_last,
                "position" => $this->employee->current_position_name,
            ],
        ];
    }
}

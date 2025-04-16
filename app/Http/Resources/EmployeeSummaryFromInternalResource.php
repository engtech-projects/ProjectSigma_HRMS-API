<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeSummaryFromInternalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->employee?->id,
            "first_name" => $this->employee?->first_name,
            "middle_name" => $this->employee?->middle_name,
            "family_name" => $this->employee?->family_name,
            "name_suffix" => $this->employee?->name_suffix,
            "nick_name" => $this->employee?->nick_name,
            "fullname_first" => $this->employee?->fullname_first,
            "fullname_last" => $this->employee?->fullname_last,
            "current_position" => $this->employee?->current_position_name,
            "current_salarygrade" => $this->employee?->current_salarygrade_and_step,
        ];
    }
}

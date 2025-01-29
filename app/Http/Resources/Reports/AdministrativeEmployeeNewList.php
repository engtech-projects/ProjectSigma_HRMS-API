<?php

namespace App\Http\Resources\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class AdministrativeEmployeeNewList extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "employee_id" => $this->company_employments?->employeedisplay_id,
            "fullname" => $this['fullname_last'],
            "designation" => $this->current_position_name,
            "section" => $this->current_assignment_names,
            "date_hired" => Carbon::parse($this->company_employments?->date_hired)->format('F j, Y'),
            "current_position_name" => $this->current_position_name,
        ];
    }
}

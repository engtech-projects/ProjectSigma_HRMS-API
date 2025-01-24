<?php

namespace App\Http\Resources\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class AdministrativeEmployeeLeaves extends JsonResource
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
            "section" => ($this->current_employment->work_location === 'Project Code') ? "Project" : "Department",
            "current_position_name" => $this->current_position_name,
            "total_days_leaves" => collect($this->employee_leave)->sum('number_of_days'),
        ];
    }
}

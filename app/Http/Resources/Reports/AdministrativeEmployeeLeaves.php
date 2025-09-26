<?php

namespace App\Http\Resources\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdministrativeEmployeeLeaves extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $leavesDailyDuration = collect($this->employee_leave)->map(function ($leave) {
            return array_sum($leave['daily_date_durations']);
        })->all();
        $total_leave = array_sum($leavesDailyDuration);

        return [
            "fullname" => $this['fullname_last'],
            "employee_id" => $this->company_employments?->employeedisplay_id,
            "designation" => $this->current_position_name,
            "section" => $this->current_assignment_names,
            "total_days_leaves" => $total_leave,
        ];
    }
}

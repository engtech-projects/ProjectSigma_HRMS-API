<?php

namespace App\Http\Resources\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PortalMonitoringManpowerRequest extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray(Request $request): array
    {
        $approvals = $this->summary_approvals;
        return [
            "employee_name" => $this['department']->department_name,
            "current_work_location" => $this['work_location'],
            "current_salary_type" => $this['employment_type'],
            "old_position" => $this['nature_of_request'],
            "new_work_location" => $this['age_range'],
            "new_salary_type" => $this["status"],
            "new_position" => $this["gender"],
            "effectivity_date" => $this["educational_requirement"],
            "date_requested" => $this->date_requested_human,
            "requested_by" => $this->created_by_user_name,
            "request_status" => $this['request_status'],
            "days_delayed_filling" => $this->days_delayed_filing,
            "date_approved" => $this->date_approved_date_human,
            "approvals" => $approvals
        ];
    }
}

<?php

namespace App\Http\Resources\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PortalMonitoringPanTransfer extends JsonResource
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
            "employee_name" => $this['employee']->fullname_last,
            "current_work_location" => $this['employee']->current_position_name,
            "current_salary_type" => $this['employee']->current_employment?->salary_type,
            "old_position" => $this['employee']->current_employment?->position?->name,
            "new_work_location" => $this->work_location,
            "new_salary_type" => $this->salary_type,
            "new_position" => $this['position']?->name,
            "effectivity_date" => $this->date_effictivity_human,
            "date_requested" => $this->request_created_at,
            "requested_by" => $this->created_by_user_name,
            "request_status" => $this["request_status"],
            "days_delayed_filling" => $this->days_delayed_filing,
            "date_approved" => $this->date_approved_date_human,
            "approvals" => $approvals,
        ];
    }
}

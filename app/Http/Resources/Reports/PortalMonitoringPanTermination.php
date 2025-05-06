<?php

namespace App\Http\Resources\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PortalMonitoringPanTermination extends JsonResource
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
            "designation" => $this['employee']->current_position_name,
            "section" => $this['employee']->current_assignment_names,
            "last_day_worked" => $this['last_day_worked'],
            "termination_type" => $this['employee']->fullname_last,
            "termination_reason" => $this['type_of_termination'],
            "eligible_for_rehire" => $this['eligible_for_rehire'],
            "effectivity_date" => $this->date_requested_human,
            "date_requested" => $this->request_created_at,
            "requested_by" => $this->created_by_user_name,
            "requested_status" => $this["request_status"],
            "days_delayed_filling" => $this->days_delayed_filing,
            "date_approved" => $this->date_approved_date_human,
            "approvals" => $approvals,
        ];
    }
}

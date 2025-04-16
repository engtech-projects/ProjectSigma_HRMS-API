<?php

namespace App\Http\Resources\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use App\Models\Users;

class PortalMonitoringFailureToLog extends JsonResource
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
            "date_of_failure_to_log" => $this->date_human,
            "date_filled" => $this->created_at_date_human,
            "prepared_by" => $this->created_by_user_name,
            "request_status" => $this['request_status'],
            "no_of_days_delayed_filling" => $this->days_delayed_filing,
            "date_approved" => $this->date_approved_date_human,
            "approvals" => $approvals
        ];
    }
}

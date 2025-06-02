<?php

namespace App\Http\Resources\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class PortalMonitoringTravelOrder extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray(Request $request): array
    {
        $approvals = $this->summary_approvals;
        $main = collect($this['employees'])->map(function ($employee) use ($approvals) {
            return [
                'employee_name' => $employee['fullname_last'],
                'designation' => $employee->current_position_name,
                'section' => $employee->current_assignment_names,
                'date_of_travel_order_from' => $this->date_of_travel_human,
                'date_of_travel_order_to' => Carbon::parse($this->date_time_end_human)->format('F j, Y'),
                'date_filled' => $this->created_at_date_human,
                'prepared_by' => $this->created_by_user_name,
                'request_status' => $this['request_status'],
                'days_delayed_filling' => $this->days_delayed_filing,
                'date_approved' => $this->date_approved_date_human,
                'approvals' => $approvals,
            ];
        })->toArray();
        return $main;
    }
}

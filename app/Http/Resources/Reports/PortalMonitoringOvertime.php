<?php

namespace App\Http\Resources\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use App\Models\Users;

class PortalMonitoringOvertime extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $approvals = collect($this['approvals'])->map(function ($approval) {
            $updateDateApproved = $this->date_approved_date ? Carbon::parse($this->date_approved_date) : null;
            $approval['no_of_days_approved_from_the_date_filled'] = null;
            if ($updateDateApproved) {
                $approval['no_of_days_approved_from_the_date_filled'] = $updateDateApproved->diffInDays($this->created_at);
            }
            $user = Users::with('employee')->find($approval['user_id']);
            $employee = $user?->employee?->fullname_first ?? "SYSTEM ADMINISTRATOR";
            return  $employee . ' - ' . $approval['status'] . ' - ' . ($approval['no_of_days_approved_from_the_date_filled'] ?? '0');
        })->implode(", ");

        $main = collect($this['employees'])->map(function ($employee) use ($approvals) {
            return [
                'employee_name' => $employee['fullname_last'],
                'designation' => $employee->current_position_name,
            ];
        });

        foreach ($main as $data) {
            return [
                'id' => $this['id'],
                'employee_name' => $data['employee_name'],
                'designation' => $data['designation'],
                'section' => $this->section_name,
                'date_of_overtime' => $this->overtime_date_human,
                'prepared_by' => $this->created_by_full_name,
                'request_status' => $this['request_status'],
                'days_delayed_filling' => $this->days_delayed_filling,
                'date_approved' => $this->date_approved_date,
                'approvals' => $approvals,
            ];
        }
    }
}

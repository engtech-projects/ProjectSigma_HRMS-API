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
        $dateApproved = collect($request["approvals"])
        ->whereNotNull('date_approved')
        ->pluck('date_approved')
        ->sortDesc()
        ->first();

        $request["date_approved"] = $dateApproved ? Carbon::parse($dateApproved)->format('F j, Y') : null;

        $approvals = collect($this['approvals'])->map(function ($approval) use ($dateApproved) {
            $updateDateApproved = $dateApproved ? Carbon::parse($dateApproved) : null;
            $approval['no_of_days_approved_from_the_date_filled'] = null;
            if (!is_null($approval['date_approved']) && $updateDateApproved) {
                $approval['no_of_days_approved_from_the_date_filled'] = $updateDateApproved->diffInDays($approval['date_approved']);
            }
            $user = Users::with('employee')->find($approval['user_id']);
            $employee = $user?->employee?->fullname_first ?? "SYSTEM ADMINISTRATOR";
            return  $employee . ' - ' . $approval['status'] . ' - ' . ($approval['no_of_days_approved_from_the_date_filled'] ?? '0');
        })->implode(", ");

        $main = collect($this['employees'])->map(function ($employee) use ($approvals) {
            return [
                'employee_name' => $employee['fullname_last'],
                'designation' => $employee->current_position_name,
                'section' => $employee->current_assignment_names,
            ];
        });

        foreach ($main as $data) {
            return [
                'id' => $this['id'],
                'employee_name' => $data['employee_name'],
                'designation' => $data['designation'],
                'section' => $data['section'],
                'date_of_overtime' => $request['overtime_date'] ? Carbon::parse($request['overtime_date'])->format('F j, Y') : null,
                'prepared_by' => $this->created_by_full_name,
                'request_status' => $this['request_status'],
                'days_delayed_filling' => $this->days_delayed_filling,
                'approvals' => $approvals,
                'date_approved' => $dateApproved ? Carbon::parse($dateApproved)->format('F j, Y') : null,
            ];
        }
    }
}

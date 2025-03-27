<?php

namespace App\Http\Resources\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

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
            return $approval['employee_name'] . ' - ' . $approval['status'] . ' - ' . ($approval['no_of_days_approved_from_the_date_filled'] ?? '0');
        });

        return [
            'id' => $this['id'],
            'employee_name' => $this['employee_name'],
            'designation' => $this['designation'],
            'section' => $this['section'],
            'date_of_overtime' => $this['date_of_overtime'],
            'prepared_by' => $this['prepared_by'],
            'request_status' => $this['request_status'],
            'days_delayed_filling' => $this['days_delayed_filling'],
            'date_approved' => $this['date_approved'],
            'approvals' => $approvals,
        ];
    }
}

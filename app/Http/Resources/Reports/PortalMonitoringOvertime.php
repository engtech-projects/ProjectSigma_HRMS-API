<?php

namespace App\Http\Resources\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PortalMonitoringOvertime extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $approvals = $this->summary_approvals;
        $main = collect($this['employees'])->map(function ($employee) {
            return [
                'employee_name' => $employee['fullname_last'],
                'designation' => $employee->current_position_name,
            ];
        });

        $returnData = [];
        foreach ($main as $data) {
            $returnData[] = [
                'employee_name' => $data['employee_name'],
                'designation' => $data['designation'],
                'section' => $this->section_name,
                'date_of_overtime' => $this->overtime_date_human,
                'prepared_by' => $this->created_by_full_name,
                'request_status' => $this['request_status'],
                'days_delayed_filling' => $this->days_delayed_filing,
                'date_approved' => $this->date_approved_date_human,
                'approvals' => $approvals,
            ];
        }
        $flattenedData = collect($returnData)->flatMap(fn ($group) => collect($group))->all();
        return $flattenedData;
    }
}

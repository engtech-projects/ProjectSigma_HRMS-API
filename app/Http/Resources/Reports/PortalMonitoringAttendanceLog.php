<?php

namespace App\Http\Resources\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PortalMonitoringAttendanceLog extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray(Request $request): array
    {
        return [
            "employee_name" => $this['employee_name'],
            "designation" => $this['designation'],
            "section" => $this['section'],
            "time_in_am" => $this['time_in_am'],
            "time_out_am" => $this['time_out_am'],
            "time_in_pm" => $this['time_in_pm'],
            "time_out_pm" => $this['time_out_pm'],
        ];
    }
}

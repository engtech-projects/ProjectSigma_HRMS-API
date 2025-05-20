<?php

namespace App\Http\Resources\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PortalMonitoringAttendanceLogSummary extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray(Request $request): array
    {
        return [
            "total_time_in_am" => $this['total_time_in_am'],
            "total_time_out_am" => $this['total_time_out_am'],
            "total_time_in_pm" => $this['total_time_in_pm'],
            "total_time_out_pm" => $this['total_time_out_pm'],
        ];
    }
}

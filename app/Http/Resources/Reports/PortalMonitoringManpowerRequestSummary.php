<?php

namespace App\Http\Resources\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PortalMonitoringManpowerRequestSummary extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray(Request $request): array
    {
        return [
            "requested_position" => $this['position']->name,
            "request_status" => $this['fill_status'],
            "total_number_requested" => $this->manpower_applicants_count,
            "total_number_unserved" => $this->manpower_applicants_pending_and_process_count,
            "total_number_served" => $this->manpower_applicants_hired_count,
        ];
    }
}

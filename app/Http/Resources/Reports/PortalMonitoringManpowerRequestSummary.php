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
            "requested_position" => $this["name"],
            "total_number_requested" => $this["total_number_requested"],
            "total_number_unserved" => $this["total_number_unserved"],
            "total_number_served" => $this["total_number_served"],
        ];
    }
}

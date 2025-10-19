<?php

namespace App\Http\Resources\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PortalMonitoringTravelOrderSummary extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "employee_name" => $this['fullname_last'],
            "total_travel_order_filed" => $this['total_travel_order']
        ];
    }
}

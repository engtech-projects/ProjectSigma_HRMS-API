<?php

namespace App\Http\Resources\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use App\Models\Users;

class PortalMonitoringOvertimeSummary extends JsonResource
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
            "overtime_filled" => $this['total_filled_overtime']
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OvertimeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "employee_id" => $this->employee_id,
            "project_id" => $this->project_id,
            "department_id" => $this->department_id,
            "overtime_date" => $this->overtime_date->format('F j, Y'),
            "overtime_start_time" => $this->overtime_start_time,
            "overtime_end_time" => $this->overtime_end_time,
            "reason" => $this->reason,
            "request_status" => $this->request_status,
            "approvals" => ApprovalAttributeResource::collection($this->approvals),
            "prepared_by" => $this->prepared_by,
        ];
    }
}

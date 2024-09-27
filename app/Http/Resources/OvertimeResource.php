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
            "employees" => $this->employees,
            "overtime_date" => $this->overtime_date->format('F j, Y'),
            "overtime_start_time" => $this->overtime_start_time,
            "overtime_end_time" => $this->overtime_end_time,
            "start_time_human" => $this->start_time_human,
            "end_time_human" => $this->end_time_human,
            "meal_deduction" => $this->meal_deduction,
            "reason" => $this->reason,
            "charging_name" => $this->charging_name,
            "project" => $this->project,
            "department" => $this->department,
            "approvals" => ApprovalAttributeResource::collection($this->approvals),
            "next_approval" => $this->getNextPendingApproval(),
            "created_by" => $this->created_by_user_name,
            "request_status" => $this->request_status,
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeLeaveResource extends JsonResource
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
            "type" => $this->type,
            "other_absence" => $this->other_absence,
            "date_of_absence_from" => $this->date_of_absence_from,
            "date_of_absence_to" => $this->date_of_absence_to,
            "reason_for_absence" => $this->reason_for_absence,
            "approvals" => ApprovalAttributeResource::collection($this->approvals),
            "requested_by_user" => $this->whenLoaded('user', function ($user) {
                return [
                    "name" => $user->employee?->fullname_last,
                    "type" => $user->type
                ];
            }),
            "employee" => $this->employee,
            "department" => $this->department,
            "project" => $this->project,
        ];
    }
}

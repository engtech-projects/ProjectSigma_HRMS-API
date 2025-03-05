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
            "type" => $this->leave->leave_name,
            "number_of_days" => $this->number_of_days,
            "leave" => $this->leave->leave_name,
            "other_absence" => $this->other_absence,
            "date_of_absence_from" => $this->date_of_absence_from->format('F j, Y'),
            "date_of_absence_to" => $this->date_of_absence_to->format('F j, Y'),
            "reason_for_absence" => $this->reason_for_absence,
            "approvals" => ApprovalAttributeResource::collection($this->approvals),
            "next_approval" => $this->getNextPendingApproval(),
            "requested_by_user" => $this->whenLoaded('user', function ($user) {
                return [
                    "name" => $user->employee?->fullname_last,
                    "type" => $user->type
                ];
            }),
            "employee" => $this->employee,
            "employeePositionName" => $this->employee->current_position_name,
            "department" => $this->department,
            "project" => $this->project,
            "request_status" => $this->request_status,
            "with_pay" => $this->with_pay,
            "created_by" => $this->created_by,
            "created_by_user" => $this->created_by_user_name,
            "created_at_human" => $this->created_at_human
        ];
    }
}

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
            "data" => [
                "id" => $this->id,
                "employee_id" => $this->employee_id,
                "project_id" => $this->project_id,
                "type" => $this->type,
                "number_of_days" => $this->number_of_days,
                "leave" => $this->whenLoaded('leave', function ($leave) {
                    return $leave->leave_name;
                }),
                "other_absence" => $this->other_absence,
                "date_of_absence_from" => $this->date_of_absence_from->format('F j, Y'),
                "date_of_absence_to" => $this->date_of_absence_to->format('F j, Y'),
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
                "request_status" => $this->request_status,
                "with_pay" => $this->with_pay,
            ],
            "links" =>  $this->links,
            "current_page" =>  $this->current_page,
            "first_page_url" =>  $this->first_page_url,
            "from" =>  $this->from,
            "last_page" =>  $this->last_page,
            "last_page_url" =>  $this->last_page_url,
            "next_page_url" =>  $this->next_page_url,
            "path" =>  $this->path,
            "per_page" =>  $this->per_page,
            "prev_page_url" =>  $this->prev_page_url,
            "to" =>  $this->to,
            "total" =>  $this->total,
        ];
    }
}

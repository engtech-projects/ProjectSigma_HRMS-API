<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ManpowerRequestResource extends JsonResource
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
            "requesting_department" => $this->requesting_department,
            "requesting_department_name" => $this->department->department_name ?? 'NO DEPARTMENT',
            "date_requested" => $this->date_requested,
            "date_required" => $this->date_required,
            "position" => $this->position,
            "employment_type" => $this->employment_type,
            "brief_description" => $this->brief_description,
            "job_description_attachment" => $this->job_description_attachment,
            "job_applicants" => $this->whenLoaded('job_applicants', JobApplicantResource::collection($this->job_applicants)),
            "nature_of_request" => $this->nature_of_request,
            "age_range" => $this->age_range,
            "status" => $this->status,
            "gender" => $this->gender,
            "educational_requirement" => $this->educational_requirement,
            "preferred_qualifications" => $this->preferred_qualifications,
            "approvals" => ApprovalAttributeResource::collection($this->approvals),
            "next_approval" => $this->getNextPendingApproval(),
            "remarks" => $this->remarks,
            "request_status" => $this->request_status,
            "fill_status" => $this->fill_status,
            "charged_to" => $this->charged_to,
            "breakdown_details" => $this->breakdown_details,
            "requested_by_user" => $this->created_by_user,
            "requested_by_user_name" => $this->created_by_user_name,
        ];
        //return parent::toArray($request);
    }
}

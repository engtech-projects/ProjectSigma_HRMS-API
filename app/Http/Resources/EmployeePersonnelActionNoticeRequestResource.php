<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeePanRequestResource extends JsonResource
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
            "type" => $this->type,
            "employee_id" => $this->employee_id,
            "fullname" => $this->employee?->fullname_last,
            "date_of_effictivity" => $this->date_of_effictivity->format('F j, Y'),
            "designation_position" => $this->designation_position,
            "hire_source" => $this->work_location,
            "work_location" => $this->work_location,
            "type_of_termination" => $this->type_of_termination,
            "reasons_for_termination" => $this->reasons_for_termination,
            "eligible_for_rehire" => $this->eligible_for_rehire,
            "last_day_worked" => $this->last_day_worked,
            "created_by" => $this->created_by,
            "request_created_at" => $this->request_created_at,
            "pan_job_applicant_id" => $this->pan_job_applicant_id,
            "salary_grades" => $this->salary_grades,
            "request_status" => $this->request_status,
            "employment_status" => $this->employment_status,
            "comments" => $this->comments,
            "section_department_id" => $this->section_department_id,
            "new_section_id" => $this->new_section_id,
            "department" => $this->whenLoaded('department'),
            "position" => $this->whenLoaded('position'),
            "approvals" => ApprovalAttributeResource::collection($this->approvals),
        ];
        //return parent::toArray($request);
    }
}

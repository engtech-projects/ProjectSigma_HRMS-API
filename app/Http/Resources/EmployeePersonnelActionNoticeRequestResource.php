<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeePersonnelActionNoticeRequestResource extends JsonResource
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
            "type" => $this->type,
            "date_of_effictivity" => $this->date_of_effictivity->format('F j, Y'),
            "designation_position" => $this->designation_position,
            "hire_source" => $this->work_location,
            "work_location" => $this->work_locationm,
            "new_location" => $this->new_location,
            "new_employment_status" => $this->new_employment_status,
            "new_position" => $this->new_position,
            "type_of_termination" => $this->type_of_termination,
            "reasons_for_termination" => $this->reasons_for_termination,
            "eligible_for_rehire" => $this->eligible_for_rehire,
            "last_day_worked" => $this->last_day_worked,
            "approvals" => ApprovalAttributeResource::collection($this->approvals),
            "created_by" => $this->created_by,
            "request_created_at" => $this->request_created_at,
            "new_salary_grades" => $this->new_salary_grades,
            "pan_job_applicant_id" => $this->pan_job_applicant_id,
            "salary_grades" => $this->salary_grades,
            "request_status" => $this->request_status,
            "employement_status" => $this->employement_status,
            "comments" => $this->comments,
            "section_department_id" => $this->section_department_id,
            "new_section_id" => $this->new_section_id,
            "fullname" => $this->employee?->fullname_last,
            "department" => $this->whenLoaded('department'),



        ];
        //return parent::toArray($request);
    }
}

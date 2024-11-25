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
            "date_of_effictivity" => $this->date_of_effictivity->format('F j, Y'),
            "type" => $this->type,
            "pan_job_applicant_id" => $this->pan_job_applicant_id,
            "jobapplicant" => $this->whenLoaded('jobapplicantonly', function () {
                return [
                    "fullname_first" => $this->jobapplicantonly->fullname_first,
                    "fullname_last" => $this->jobapplicantonly->fullname_last,
                ];
            }),
            "employee_id" => $this->employee_id,
            "employee" => $this->whenLoaded("employee", function () {
                return [
                    "fullname_first" => $this->employee->fullname_first,
                    "fullname_last" => $this->employee->fullname_last,
                ];
            }),
            "applicant_employee" => $this->full_name,
            "hire_source" => $this->hire_source,
            "employment_status" => $this->employment_status,
            "designation_position" => $this->designation_position, // position_id
            "position" => $this->whenLoaded('position'),
            "salary_type" => $this->salary_type,
            "salary_grades" => $this->salary_grades,
            "salarygrade" => $this->whenLoaded('salarygrade'),
            "work_location" => $this->work_location,
            "section_department_id" => $this->section_department_id, // department_id
            "department_name" => $this->department_name,
            "project_names" => $this->project_names->implode(", "),
            "department" => $this->whenLoaded('department'),
            "type_of_termination" => $this->type_of_termination,
            "reasons_for_termination" => $this->reasons_for_termination,
            "eligible_for_rehire" => $this->eligible_for_rehire,
            "last_day_worked" => $this->last_day_worked,
            "created_by" => $this->created_by,
            "request_created_at" => $this->request_created_at,
            "created_at_human" => $this->created_at_human,
            "request_status" => $this->request_status,
            "comments" => $this->comments,
            "created_by_user_name" => $this->created_by_user_name,
            "approvals" => ApprovalAttributeResource::collection($this->approvals),
            "next_approval" => $this->getNextPendingApproval(),
        ];
        //return parent::toArray($request);
    }
}

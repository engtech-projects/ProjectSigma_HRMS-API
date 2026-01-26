<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InternalWorkExpResource extends JsonResource
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
            "position_title" => $this->position_title,
            "employment_status" => $this->employment_status,
            "immediate_supervisor" => $this->emmediate_supervisor,
            "actual_salary" => $this->actual_salary,
            "work_location" => $this->work_location,
            "hire_source" => $this->hire_resource,
            "status" => $this->status,
            "date_from" => $this->date_from,
            "date_to" => $this->date_to,
            "salary_grades" => $this->salary_grades,
            "department_id" => $this->department_id,
            "employee_salarygrade" => $this->whenLoaded('employee_salarygrade'),
            "department" => $this->whenLoaded('department'),
        ];
        /* return parent::toArray($request); */
    }
}

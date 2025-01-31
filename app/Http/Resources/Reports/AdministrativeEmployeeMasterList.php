<?php

namespace App\Http\Resources\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class AdministrativeEmployeeMasterList extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "employee_id" => $this->company_employments?->employeedisplay_id,
            "date_hired" => $this->company_employments?->employee_date_hired,
            "first_name" => $this['first_name'],
            "middle_name" => $this['middle_name'],
            "family_name" => $this['family_name'],
            "name_suffix" => $this['name_suffix'],
            "nick_name" => $this['nick_name'],
            "present_address" => $this->present_address?->complete_address,
            "permanent_address" => $this->permanent_address?->complete_address,
            "mobile_number" => $this['mobile_number'],
            "date_of_birth" => $this['date_of_birth'] ? Carbon::parse($this['date_of_birth'])->format('F j, Y') : "Date Birth N/A",
            "place_of_birth" => $this['place_of_birth'],
            "citizenship" => $this['citizenship'],
            "blood_type" => $this['blood_type'],
            "gender" => $this['gender'],
            "religion" => $this['religion'],
            "civil_status" => $this['civil_status'],
            "height" => $this['height'],
            "weight" => $this['weight'],
            "date_of_marriage" => $row->date_marriage,
            "father" => $this->father?->name,
            "mother" => $this->mother?->name,
            "spouse" => $this->spouse?->name,
            "spouse_date_of_birth" => $this->spouse?->date_of_birth ? Carbon::parse($this->spouse?->date_of_birth)->format('F j, Y') : "Date Birth N/A",
            "spouse_occupation" => $this->spouse?->name,
            "children_summary" => $this->child->pluck('name_bday')->implode(', '),
            "contact_person" => $this->contact_person?->name,
            "contact_person_address" => $this->contact_person?->address,
            "contact_person_contact_no" => $this->contact_person?->contact_no,
            "contact_person_relationship" => $this->contact_person?->relationship,
            "employee_education_elementary" => $this->employee_education_elementary?->education,
            "employee_education_secondary" => $this->employee_education_secondary?->education,
            "employee_education_college" => $this->employee_education_college?->education,
            "sss_number" => $this->company_employments?->sss_number,
            "phic_number" => $this->company_employments?->phic_number,
            "pagibig_number" => $this->company_employments?->pagibig_number,
            "tin_number" => $this->company_employments?->tin_number,
            "work_location" => $this->current_employment->work_location,
            "current_position_name" => $this->current_position_name,
            "section" => $this->current_assignment_names,
            "salary_grade" => $this->current_salarygrade_and_step,
        ];
    }
}

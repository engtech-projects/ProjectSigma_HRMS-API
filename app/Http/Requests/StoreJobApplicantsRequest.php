<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobApplicantsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            "workexperience" => json_decode($this->workexperience, true),
            "education" => json_decode($this->education, true),
            "children" => json_decode($this->children, true),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'manpowerrequests_id' => [
                "required",
                "integer",
                "exists:manpower_requests,id",
            ],
            'name_suffix' => [
                "required",
                "string",
            ],
            'application_letter_attachment' => [
                "required",
                "max:10000",
                "mimes:application/msword,doc,docx,pdf,zip",
            ],
            'resume_attachment' => [
                "required",
                "max:10000",
                "mimes:application/msword,doc,docx,pdf,zip",
            ],
            'status' => [
                "required",
                "string",
                'in:Contract Extended,Contact Extended,Pending,Interviewed,Rejected,Hired,For Hiring,Test,Interview,Reference Checking,Medical Examination'
            ],
            'lastname' => [
                "required",
                "string",
                "max:35"
            ],
            'firstname' => [
                "required",
                "string",
                "max:35"
            ],
            'middlename' => [
                "required",
                "string",
                "max:35"
            ],
            'date_of_application' => [
                "required",
                "date",
            ],
            'date_of_birth' => [
                "required",
                "date",
            ],
            'pre_address_street' => [
                "required",
                "string",
            ],
            'pre_address_brgy' => [
                "required",
                "string",
            ],
            'pre_address_city' => [
                "required",
                "string",
            ],
            'pre_address_zip' => [
                "required",
                "string",
            ],
            'pre_address_province' => [
                "required",
                "string",
            ],
            'per_address_street' => [
                "required",
                "string",
            ],
            'per_address_brgy' => [
                "required",
                "string",
            ],
            'per_address_city' => [
                "required",
                "string",
            ],
            'per_address_zip' => [
                "required",
                "string",
            ],
            'per_address_province' => [
                "required",
                "string",
            ],
            'contact_info' => [
                "required",
                "string",
                "min:11",
                "max:11",
            ],
            'email' => [
                "required",
                "string",
                "max:35"
            ],
            'how_did_u_learn_about_our_company' => [
                "required",
                "string",
            ],
            'desired_position' => [
                "required",
                "string",
            ],
            'currently_employed' => [
                "required",
                "string",
                'in:Yes,No'
            ],
            'name_of_spouse' => [
                "nullable",
                "string",
                "max:55"
            ],
            'date_of_birth_spouse' => [
                "nullable",
                "date",
            ],
            'occupation_spouse' => [
                "nullable",
                "string",
            ],
            'telephone_spouse' => [
                "nullable",
                "string",
                "min:11",
                "max:11",
            ],
            'children.*' => [
                "nullable",
                "array",
                "required_array_keys:name,birthdate"
            ],
            'children.*.name' => [
                "required",
                "string",
            ],
            'children.*.birthdate' => [
                "required",
                "date",
            ],
            'icoe_name' => [
                "required",
                "string",
            ],
            'icoe_relationship' => [
                "required",
                "string",
            ],
            'telephone_icoe' => [
                "required",
                "string",
                "min:11",
                "max:11",
            ],
            'education.*' => [
                "required",
                "array",
                "required_array_keys:elementary_name,elementary_education,elementary_period_attendance_to,elementary_period_attendance_from,elementary_year_graduated,secondary_name,secondary_education,secondary_period_attendance_to,secondary_period_attendance_from,secondary_year_graduated,vocationalcourse_name,vocationalcourse_education,vocationalcourse_period_attendance_to,vocationalcourse_period_attendance_from,vocationalcourse_year_graduated,college_name,college_education,college_period_attendance_to,college_period_attendance_from,college_year_graduated,graduatestudies_name,graduatestudies_education,graduatestudies_period_attendance_to,graduatestudies_period_attendance_from,graduatestudies_year_graduated"
            ],
            'education.*.elementary_name' => [
                "required",
                "string",
            ],
            'education.*.elementary_education' => [
                "required",
                "string",
            ],
            'education.*.elementary_period_attendance_to' => [
                "required",
                "string",
            ],
            'education.*.elementary_period_attendance_from' => [
                "required",
                "string",
            ],
            'education.*.elementary_year_graduated' => [
                "required",
                "string",
            ],
            'education.*.secondary_name' => [
                "required",
                "string",
            ],
            'education.*.secondary_education' => [
                "required",
                "string",
            ],
            'education.*.secondary_period_attendance_to' => [
                "required",
                "string",
            ],
            'education.*.secondary_period_attendance_from' => [
                "required",
                "string",
            ],
            'education.*.secondary_year_graduated' => [
                "required",
                "string",
            ],
            'education.*.vocationalcourse_name' => [
                "required",
                "string",
            ],
            'education.*.vocationalcourse_education' => [
                "required",
                "string",
            ],
            'education.*.vocationalcourse_period_attendance_to' => [
                "required",
                "string",
            ],
            'education.*.vocationalcourse_period_attendance_from' => [
                "required",
                "string",
            ],
            'education.*.vocationalcourse_year_graduated' => [
                "required",
                "string",
            ],
            'education.*.college_name' => [
                "required",
                "string",
            ],
            'education.*.college_education' => [
                "required",
                "string",
            ],
            'education.*.college_period_attendance_to' => [
                "required",
                "string",
            ],
            'education.*.college_period_attendance_from' => [
                "required",
                "string",
            ],
            'education.*.college_year_graduated' => [
                "required",
                "string",
            ],
            'education.*.graduatestudies_name' => [
                "required",
                "string",
            ],
            'education.*.graduatestudies_education' => [
                "required",
                "string",
            ],
            'education.*.graduatestudies_period_attendance_to' => [
                "required",
                "string",
            ],
            'education.*.graduatestudies_period_attendance_from' => [
                "required",
                "string",
            ],
            'education.*.graduatestudies_year_graduated' => [
                "required",
                "string",
            ],
            'workexperience.*' => [
                "nullable",
                "array",
                "required_array_keys:inclusive_dates_from,inclusive_dates_to,position_title,dpt_agency_office_company,monthly_salary,status_of_appointment"
            ],
            'workexperience.*.inclusive_dates_from' => [
                "nullable",
                "date",
            ],
            'workexperience.*.inclusive_dates_to' => [
                "nullable",
                "date",
            ],
            'workexperience.*.position_title' => [
                "nullable",
                "string",
            ],
            'workexperience.*.dpt_agency_office_company' => [
                "nullable",
                "string",
            ],
            'workexperience.*.monthly_salary' => [
                "nullable",
                "string",
            ],
            'workexperience.*.status_of_appointment' => [
                "nullable",
                "string",
            ],
            'place_of_birth' => [
                "required",
                "string",
            ],
            'blood_type' => [
                "required",
                "string",
            ],
            'date_of_marriage' => [
                "nullable",
                "string",
            ],
            'sss' => [
                "nullable",
                "string",
            ],
            'philhealth' => [
                "nullable",
                "string",
            ],
            'pagibig' => [
                "nullable",
                "string",
            ],
            'tin' => [
                "nullable",
                "string",
            ],
            'citizenship' => [
                "required",
                "string",
            ],
            'religion' => [
                "required",
                "string",
            ],
            'height' => [
                "required",
                "string",
            ],
            'weight' => [
                "required",
                "string",
            ],
            'father_name' => [
                "required",
                "string",
            ],
            'mother_name' => [
                "required",
                "string",
            ],
            'gender' => [
                "required",
                "string",
            ],
            'civil_status' => [
                "required",
                "string",
            ],
            'icoe_street' => [
                "required",
                "string",
            ],
            'icoe_brgy' => [
                "required",
                "string",
            ],
            'icoe_city' => [
                "required",
                "string",
            ],
            'icoe_zip' => [
                "required",
                "string",
            ],
            'icoe_province' => [
                "required",
                "string",
            ],
            'remarks' => [
                "nullable",
                "string",
            ],
        ];
    }
}

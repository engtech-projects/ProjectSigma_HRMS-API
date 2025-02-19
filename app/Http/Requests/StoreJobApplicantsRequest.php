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
        $this->merge(
            array_map(function ($value) {
                return $value === 'n/a' || $value === 'N/A' ? null : $value;
            }, $this->all())
        );

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
            'firstname' => [
                "required",
                "string",
                "max:35"
            ],
            'middlename' => [
                "nullable",
                "string",
                "max:35"
            ],
            'lastname' => [
                "required",
                "string",
                "max:35"
            ],
            'name_suffix' => [
                "nullable",
                "string",
            ],
            'nickname' => [
                "required",
                "string",
                "max:250"
            ],
            'date_of_birth' => [
                "nullable",
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
                "nullable",
                "string",
                "min:11",
                "max:11",
            ],
            'atm' => [
                "nullable",
                "string",
                "min:10",
                "max:16",
            ],
            'email' => [
                "nullable",
                "string",
                "max:35"
            ],
            'how_did_u_learn_about_our_company' => [
                "nullable",
                "string",
            ],
            'desired_position' => [
                "nullable",
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
                "string",
            ],
            'occupation_spouse' => [
                "nullable",
                "string",
            ],
            'telephone_spouse' => [
                "nullable",
                "string",
            ],
            "children" => [
                "present",
                "nullable",
                "array"
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
            'icoe_occupation' => [
                "required",
                "string",
            ],
            'icoe_date_of_birth' => [
                "required",
                "date",
            ],
            'telephone_icoe' => [
                "required",
                "string",
            ],
            "education" => [
                "nullable",
                "array"
            ],
            'education.*' => [
                "nullable",
                "array",
                "required_array_keys:type,name,education,period_attendance_from,period_attendance_to,year_graduated,honors_received"
            ],
            'education.*.type' => [
                "nullable",
                "string",
                "in:elementary,secondary,vocational_course,college,graduate_studies",
            ],
            'education.*.name' => [
                "nullable",
                "string",
            ],
            'education.*.education' => [
                "nullable",
                "string",
            ],
            'education.*.period_attendance_from' => [
                "nullable",
                "string",
            ],
            'education.*.period_attendance_to' => [
                "nullable",
                "string",
            ],
            'education.*.year_graduated' => [
                "nullable",
                "string",
            ],
            'education.*.honors_received' => [
                "nullable",
                "string",
            ],
            'education.*.degree_earned_of_school' => [
                "nullable",
                "string",
            ],
            "workexperience" => [
                "present",
                "nullable",
                "array"
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
                "numeric",
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
                "nullable",
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
                "nullable",
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
                "nullable",
                "string",
            ],
            'mother_name' => [
                "nullable",
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
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJobApplicantsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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
                "nullable",
                "integer",
                "exists:manpower_requests,id",
            ],
            'name_suffix' => [
                "nullable",
                "string",
            ],
            'application_letter_attachment' => [
                "nullable",
                "max:10000",
                "mimes:application/msword,doc,docx,pdf,zip",
            ],
            'resume_attachment' => [
                "nullable",
                "max:10000",
                "mimes:application/msword,doc,docx,pdf,zip",
            ],
            'lastname' => [
                "nullable",
                "string",
                "max:35"
            ],
            'firstname' => [
                "nullable",
                "string",
                "max:35"
            ],
            'middlename' => [
                "nullable",
                "string",
                "max:35"
            ],
            'date_of_application' => [
                "nullable",
                "date",
            ],
            'date_of_birth' => [
                "nullable",
                "date",
            ],
            'pre_address_street' => [
                "nullable",
                "string",
            ],
            'pre_address_brgy' => [
                "nullable",
                "string",
            ],
            'pre_address_city' => [
                "nullable",
                "string",
            ],
            'pre_address_zip' => [
                "nullable",
                "string",
            ],
            'pre_address_province' => [
                "nullable",
                "string",
            ],
            'per_address_street' => [
                "nullable",
                "string",
            ],
            'per_address_brgy' => [
                "nullable",
                "string",
            ],
            'per_address_city' => [
                "nullable",
                "string",
            ],
            'per_address_zip' => [
                "nullable",
                "string",
            ],
            'per_address_province' => [
                "nullable",
                "string",
            ],
            'contact_info' => [
                "nullable",
                "string",
                "min:11",
                "max:11",
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
                "nullable",
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
                "nullable",
                "string",
            ],
            'children.*.birthdate' => [
                "nullable",
                "date",
            ],
            'icoe_name' => [
                "nullable",
                "string",
            ],
            'icoe_relationship' => [
                "nullable",
                "string",
            ],
            'telephone_icoe' => [
                "nullable",
                "string",
                "min:11",
                "max:11",
            ],
            'education.*' => [
                "nullable",
                "array",
                "nullable_array_keys:type,name,education,period_attendance_from,period_attendance_to,year_graduated,honors_received"
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
            'workexperience.*' => [
                "nullable",
                "array",
                "nullable_array_keys:inclusive_dates_from,inclusive_dates_to,position_title,dpt_agency_office_company,monthly_salary,status_of_appointment"
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
                "nullable",
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
                "nullable",
                "string",
            ],
            'religion' => [
                "nullable",
                "string",
            ],
            'height' => [
                "nullable",
                "string",
            ],
            'weight' => [
                "nullable",
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
                "nullable",
                "string",
            ],
            'civil_status' => [
                "nullable",
                "string",
            ],
            'icoe_street' => [
                "nullable",
                "string",
            ],
            'icoe_brgy' => [
                "nullable",
                "string",
            ],
            'icoe_city' => [
                "nullable",
                "string",
            ],
            'icoe_zip' => [
                "nullable",
                "string",
            ],
            'icoe_province' => [
                "nullable",
                "string",
            ]
        ];
    }
}

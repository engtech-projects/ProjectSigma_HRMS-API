<?php

namespace App\Http\Requests;

use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;

class BulkValidationRequest extends FormRequest
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
    // protected function prepareForValidation() : void{
    //     $this->merge([
    //         "employees_data" => json_decode($this->employees_data, true)
    //     ]);
    // }
    public function rules(): array
    {
        return [
            "employees_data" => 'present',
            "employees_data*.status" => 'present|in:' . Employee::EMPLOYEE_BULK_STATUS_DUPLICATE . ',' . Employee::EMPLOYEE_BULK_STATUS_UNDUPLICATE,
            "employees_data*.family_name" => 'present',
            "employees_data*.first_name" => 'present',
            "employees_data*.middle_name" => 'present',
            "employees_data*.nick_name" => 'present|nullable',
            "employees_data*.pre_street" => 'present|nullable',
            "employees_data*.mobile_number" => 'present|nullable',
            "employees_data*.per_street" => 'present|nullable',
            "employees_data*.date_of_birth" => 'present|nullable',
            "employees_data*.place_of_birth" => 'present|nullable',
            "employees_data*.citizenship" => 'present|nullable',
            "employees_data*.blood_type" => 'present|nullable',
            "employees_data*.gender" => 'present|nullable',
            "employees_data*.religion" => 'present|nullable',
            "employees_data*.civil_status" => 'present|nullable',
            "employees_data*.height" => 'present|nullable',
            "employees_data*.phic_number" => 'present|nullable',
            "employees_data*.pagibig_number" => 'present|nullable',
            "employees_data*.tin_number" => 'present|nullable',
            "employees_data*.sss_number" => 'present|nullable',
            "employees_data*.father_name" => 'present|nullable',
            "employees_data*.mother_name" => 'present|nullable',
            "employees_data*.spouse_name" => 'present|nullable',
            "employees_data*.spouse_occupation" => 'present|nullable',
            "employees_data*.spouse_contact_no" => 'present|nullable',
            "employees_data*.childrens" => 'present|nullable',
            "employees_data*.childrens_date_of_birth" => 'present|nullable',
            "employees_data*.person_to_contact_name" => 'present|nullable',
            "employees_data*.person_to_contact_street" => 'present|nullable',
            "employees_data*.person_to_contact_no" => 'present|nullable',
            "employees_data*.person_to_contact_relationship" => 'present|nullable',
            "employees_data*.previous_hospitalization" => 'present|nullable',
            "employees_data*.previous_operation" => 'present|nullable',
            "employees_data*.current_undergoing_treatment" => 'present|nullable',
            "employees_data*.convicted_crime" => 'present|nullable',
            "employees_data*.dismissed_resigned" => 'present|nullable',
            "employees_data*.pending_administrative" => 'present|nullable',
            "employees_data*.name_of_relative_working_with" => 'present|nullable',
            "employees_data*.relationship_of_relative_working_with" => 'present|nullable',
            "employees_data*.position_of_relative_working_with" => 'present|nullable',
            "employees_data*.name_of_school_elementary" => 'present|nullable',
            "employees_data*.degree_earned_of_school_elementary" => 'present|nullable',
            "employees_data*.dates_of_school_elementary" => 'present|nullable',
            "employees_data*.honor_of_school_elementary" => 'present|nullable',
            "employees_data*.name_of_school_highschool" => 'present|nullable',
            "employees_data*.degree_earned_of_school_highschool" => 'present|nullable',
            "employees_data*.dates_of_school_highschool" => 'present|nullable',
            "employees_data*.honor_of_school_highschool" => 'present|nullable',
            "employees_data*.name_of_school_college" => 'present|nullable',
            "employees_data*.degree_earned_of_school_college" => 'present|nullable',
            "employees_data*.dates_of_school_college" => 'present|nullable',
            "employees_data*.honor_of_school_college" => 'present|nullable',
            "employees_data*.vocational" => 'present|nullable',
            "employees_data*.name_of_school_vocational" => 'present|nullable',
            "employees_data*.degree_earned_of_school_vocational" => 'present|nullable',
            "employees_data*.dates_of_school_vocational" => 'present|nullable',
            "employees_data*.honor_of_school_vocational" => 'present|nullable',
            "employees_data*.master_thesis_name" => 'present|nullable',
            "employees_data*.master_thesis_date" => 'present|nullable',
            "employees_data*.doctorate_desertation_name" => 'present|nullable',
            "employees_data*.doctorate_desertation_date" => 'present|nullable',
            "employees_data*.professional_license_name" => 'present|nullable',
            "employees_data*.professional_license_date" => 'present|nullable',
            "employees_data*.reference_name" => 'present|nullable',
            "employees_data*.reference_address" => 'present|nullable',
            "employees_data*.reference_posiiton" => 'present|nullable',
            "employees_data*.reference_contact_no" => 'present|nullable',
            "employees_data*.employee_id" => 'present',
            "employees_data*.company" => 'present',
            "employees_data*.employment_status" => 'present|nullable',
            "employees_data*.position" => 'present|nullable',
            "employees_data*.department" => 'present|nullable',
            "employees_data*.division" => 'present|nullable',
            "employees_data*.imidiate_supervisor" => 'present|nullable',
        ];
    }
}

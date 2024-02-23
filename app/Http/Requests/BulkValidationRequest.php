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
    public function rules(): array
    {
        return [
            "employee_data*.status" => 'required|in:'.Employee::EMPLOYEE_BULK_STATUS_DUPLICATE.','.Employee::EMPLOYEE_BULK_STATUS_UNDUPLICATE,
            "employee_data*.family_name" => 'required',
            "employee_data*.first_name" => 'required',
            "employee_data*.middle_name" => 'required',
            "employee_data*.nick_name" => 'required|nullable',
            "employee_data*.pre_street" => 'required|nullable',
            "employee_data*.mobile_number" => 'required|nullable',
            "employee_data*.per_street" => 'required|nullable',
            "employee_data*.date_of_birth" => 'required|nullable',
            "employee_data*.place_of_birth" => 'required|nullable',
            "employee_data*.citizenship" => 'required|nullable',
            "employee_data*.blood_type" => 'required|nullable',
            "employee_data*.gender" => 'required|nullable',
            "employee_data*.religion" => 'required|nullable',
            "employee_data*.civil_status" => 'required|nullable',
            "employee_data*.height" => 'required|nullable',
            "employee_data*.phic_number" => 'required|nullable',
            "employee_data*.pagibig_number" => 'required|nullable',
            "employee_data*.tin_number" => 'required|nullable',
            "employee_data*.sss_number" => 'required|nullable',
            "employee_data*.father_name" => 'required|nullable',
            "employee_data*.mother_name" => 'required|nullable',
            "employee_data*.spouse_name" => 'required|nullable',
            "employee_data*.spouse_occupation" => 'required|nullable',
            "employee_data*.spouse_contact_no" => 'required|nullable',
            "employee_data*.childrens" => 'required|nullable',
            "employee_data*.childrens_date_of_birth" => 'required|nullable',
            "employee_data*.person_to_contact_name" => 'required|nullable',
            "employee_data*.person_to_contact_street" => 'required|nullable',
            "employee_data*.person_to_contact_no" => 'required|nullable',
            "employee_data*.person_to_contact_relationship" => 'required|nullable',
            "employee_data*.previous_hospitalization" => 'required|nullable',
            "employee_data*.previous_operation" => 'required|nullable',
            "employee_data*.current_undergoing_treatment" => 'required|nullable',
            "employee_data*.convicted_crime" => 'required|nullable',
            "employee_data*.dismissed_resigned" => 'required|nullable',
            "employee_data*.pending_administrative" => 'required|nullable',
            "employee_data*.name_of_relative_working_with" => 'required|nullable',
            "employee_data*.relationship_of_relative_working_with" => 'required|nullable',
            "employee_data*.position_of_relative_working_with" => 'required|nullable',
            "employee_data*.name_of_school_elementary" => 'required|nullable',
            "employee_data*.degree_earned_of_school_elementary" => 'required|nullable',
            "employee_data*.dates_of_school_elementary" => 'required|nullable',
            "employee_data*.honor_of_school_elementary" => 'required|nullable',
            "employee_data*.name_of_school_highschool" => 'required|nullable',
            "employee_data*.degree_earned_of_school_highschool" => 'required|nullable',
            "employee_data*.dates_of_school_highschool" => 'required|nullable',
            "employee_data*.honor_of_school_highschool" => 'required|nullable',
            "employee_data*.name_of_school_college" => 'required|nullable',
            "employee_data*.degree_earned_of_school_college" => 'required|nullable',
            "employee_data*.dates_of_school_college" => 'required|nullable',
            "employee_data*.honor_of_school_college" => 'required|nullable',
            "employee_data*.vocational" => 'required|nullable',
            "employee_data*.name_of_school_vocational" => 'required|nullable',
            "employee_data*.degree_earned_of_school_vocational" => 'required|nullable',
            "employee_data*.dates_of_school_vocational" => 'required|nullable',
            "employee_data*.honor_of_school_vocational" => 'required|nullable',
            "employee_data*.master_thesis_name" => 'required|nullable',
            "employee_data*.master_thesis_date" => 'required|nullable',
            "employee_data*.doctorate_desertation_name" => 'required|nullable',
            "employee_data*.doctorate_desertation_date" => 'required|nullable',
            "employee_data*.professional_license_name" => 'required|nullable',
            "employee_data*.professional_license_date" => 'required|nullable',
            "employee_data*.reference_name" => 'required|nullable',
            "employee_data*.reference_address" => 'required|nullable',
            "employee_data*.reference_posiiton" => 'required|nullable',
            "employee_data*.reference_contact_no" => 'required|nullable',
            "employee_data*.employee_id" => 'required',
            "employee_data*.company" => 'required',
            "employee_data*.employment_status" => 'required|nullable',
            "employee_data*.position" => 'required|nullable',
            "employee_data*.department" => 'required|nullable',
            "employee_data*.division" => 'required|nullable',
            "employee_data*.imidiate_supervisor" => 'required|nullable',
        ];
    }
}

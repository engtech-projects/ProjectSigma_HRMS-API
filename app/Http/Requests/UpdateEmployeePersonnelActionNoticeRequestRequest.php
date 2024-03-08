<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeePersonnelActionNoticeRequestRequest extends FormRequest
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
            'employee_id' => [
                "nullable",
                "integer",
                "exists:employees,id",
            ],
            'type' => [
                "nullable",
                "string",
                'in:New Hire,Termination,Transfer,Promotion'
            ],
            'date_of_effictivity' => [
                "nullable",
                "date",
            ],
            'section_department' => [
                "nullable",
                "string"
            ],
            'designation_position' => [
                "nullable",
                "string"
            ],
            'salary_grades' => [
                "nullable",
                "integer",
                "exists:salary_grade_steps,id",
            ],
            'new_salary_grades' => [
                "nullable",
                "integer",
                "exists:new_salary_grades,id",
            ],
            'pan_job_applicant_id' => [
                "nullable",
                "integer",
                "exists:job_applicants,id",
            ],
            'hire_source' => [
                "nullable",
                "string",
                'in:Internal,External'
            ],
            'work_location' => [
                "nullable",
                "string",
            ],
            'new_section' => [
                "nullable",
                "string",
            ],
            'new_location' => [
                "nullable",
                "string",
            ],
            'new_employment_status' => [
                "nullable",
                "string",
            ],
            'new_position' => [
                "nullable",
                "string",
            ],
            'type_of_termination' => [
                "nullable",
                "string",
            ],
            'reasons_for_termination' => [
                "nullable",
                "string",
            ],
            'eligible_for_rehire' => [
                "nullable",
                "string",
            ],
            'last_day_worked' => [
                "nullable",
                "string",
            ],
            'approvals' => [
                "nullable",
                "array",
            ],
            'approvals.*' => [
                "nullable",
                "array",
                "nullable_array_keys:type,user_id,status,date_approved,remarks",
            ],
            'approvals.*.type' => [
                "nullable",
                "string",
            ],
            'approvals.*.user_id' => [
                "nullable",
                "integer",
                "exists:users,id",
            ],
            'approvals.*.status' => [
                "nullable",
                "string",
            ],
            'approvals.*.date_approved' => [
                "nullable",
                "date",
            ],
            'approvals.*.remarks' => [
                "nullable",
                "string",
            ],
            'created_by' => [
                "nullable",
                "integer",
                "exists:users,id",
            ],
        ];
    }
}

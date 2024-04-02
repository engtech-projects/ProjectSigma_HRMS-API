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
                'required_if:type,!=,New Hire',
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
            'section_department_id' => [
                "nullable",
                "integer",
                "exists:departments,id",
                'required_if:type,==,New Hire',
            ],
            'designation_position' => [
                "nullable",
                "string",
                'required_if:type,==,New Hire',
            ],
            'salary_grades' => [
                "nullable",
                "integer",
                "exists:salary_grade_steps,id",
                'required_if:type,!=,Transfer',
                'required_if:type,==,New Hire|required_if:type,==,Transfer',
            ],
            'new_salary_grades' => [
                "nullable",
                "integer",
                "exists:salary_grade_steps,id",
                'required_if:type,!=,Transfer',
            ],
            'pan_job_applicant_id' => [
                "nullable",
                "integer",
                "exists:job_applicants,id",
            ],
            'hire_source' => [
                "nullable",
                "string",
                'in:Internal,External',
                'required_if:type,==,New Hire',
            ],
            'work_location' => [
                "nullable",
                "string",
                'required_if:type,==,New Hire|required_if:type,==,Transfer',
            ],
            'new_section_id' => [
                "nullable",
                "integer",
                "exists:departments,id",
                'required_if:type,==,Transfer',
            ],
            'new_location' => [
                "nullable",
                "string",
                'required_if:type,==,Transfer'
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
                'required_if:type,==,Termination'
            ],
            'reasons_for_termination' => [
                "nullable",
                "string",
                'required_if:type,==,Termination'
            ],
            'eligible_for_rehire' => [
                "nullable",
                "string",
                'required_if:type,==,Termination'
            ],
            'last_day_worked' => [
                "nullable",
                "string",
                'required_if:type,==,Termination'
            ],
            'approvals' => [
                "nullable",
                "array",
            ],
            'approvals.*' => [
                "nullable",
                "array",
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
            'comments' => [
                "nullable",
                "string",
            ],
            'employement_status' => [
                "nullable",
                "string",
                'required_if:type,==,New Hire|required_if:type,==,Promotion',
            ],
        ];
    }
}

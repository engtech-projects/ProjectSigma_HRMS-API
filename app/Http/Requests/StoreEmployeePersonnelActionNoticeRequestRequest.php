<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeePersonnelActionNoticeRequestRequest extends FormRequest
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
            "approvals" => json_decode($this->approvals, true)

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
            'employee_id' => [
                "nullable",
                "integer",
                "exists:employees,id",
                'required_if:type,==,Termination,Transfer,Promotion',
            ],
            'type' => [
                "required",
                "string",
                'in:New Hire,Termination,Transfer,Promotion'
            ],
            'date_of_effictivity' => [
                "required",
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
                'required_if:type,==,New Hire',
            ],
            'new_salary_grades' => [
                "nullable",
                "integer",
                "exists:salary_grade_steps,id",
                'required_if:type,==,Promotion',
            ],
            'pan_job_applicant_id' => [
                "nullable",
                "integer",
                "exists:job_applicants,id",
                'required_if:type,==,New Hire',
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
                'required_if:type,==,New Hire,Transfer',
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
                'required_if:type,==,Promotion'
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
                "required",
                "array",
            ],
            'approvals.*' => [
                "required",
                "array",
                "required_array_keys:type,user_id,status,date_approved,remarks",
            ],
            'approvals.*.type' => [
                "required",
                "string",
            ],
            'approvals.*.user_id' => [
                "nullable",
                "integer",
                "exists:users,id",
            ],
            'approvals.*.status' => [
                "required",
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
            'comments' => [
                "nullable",
                "string",
            ],
            'employement_status' => [
                "nullable",
                "string",
                'required_if:type,==,New Hire',
            ],
        ];
    }
}

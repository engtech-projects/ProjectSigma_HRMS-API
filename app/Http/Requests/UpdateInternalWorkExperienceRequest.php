<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInternalWorkExperienceRequest extends FormRequest
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
            'position_title' => [
                "nullable",
                "string"
            ],
            'employment_status' => [
                "nullable",
                "string"
            ],
            'department_id' => [
                "nullable",
                "integer",
                "exists:departments,id"
            ],
            'immediate_supervisor' => [
                "nullable",
                "string"
            ],
            'salary_grades' => [
                "nullable",
                "integer",
                "exists:salary_grade_steps,id",
            ],
            'actual_salary' => [
                "nullable",
                "string"
            ],
            'work_location' => [
                "nullable",
                "string",
                'in:pms,office,project_code'
            ],
            'hire_source' => [
                "nullable",
                "string",
                'in:internal,external'
            ],
            'status' => [
                "nullable",
                'in:active,inactive'
            ],
            'date_from' => [
                "nullable",
                "date",
            ],
            'date_to' => [
                "nullable",
                "date",
            ],
        ];
    }
}

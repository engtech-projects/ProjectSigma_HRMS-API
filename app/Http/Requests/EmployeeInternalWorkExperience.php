<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeInternalWorkExperience extends FormRequest
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
            'id'=> [
                "required",
                "integer",
                "exists:employee_personnel_action_notice_requests,id",
            ],
            'employee_id'=> [
                "required",
                "integer",
                "exists:employees,id",
            ],
            'position_title'=>[
                "required",
                "string"
            ],
            'employment_status'=>[
                "required",
                "string"
            ],
            'department'=>[
                "required",
                "string"
            ],
            'immediate_supervisor'=>[
                "required",
                "string"
            ],
            'salary_grades'=>[
                "required",
                "integer",
                "exists:salary_grade_steps,id",
            ],
            'actual_salary'=>[
                "required",
                "string"
            ],
            'work_location'=>[
                "required",
                "string",
                'in:pms,office,project_code'
            ],
            'hire_source'=>[
                "required",
                "string",
                'in:internal,external'
            ],
            'status'=>[
                "required",
                'in:active,inactive'
            ],
            'date_from'=>[
                "required",
                "date",
            ],
            'date_to'=>[
                "required",
                "date",
            ],
        ];
    }
}

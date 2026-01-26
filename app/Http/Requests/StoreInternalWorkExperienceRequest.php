<?php

namespace App\Http\Requests;

use App\Enums\EmploymentType;
use App\Enums\SalaryRequestType;
use App\Http\Traits\CheckAccessibility;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreInternalWorkExperienceRequest extends FormRequest
{
    use CheckAccessibility;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false; // FALSE TO DISABLE CREATING DIRECTLY
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
                "required",
                "integer",
                "exists:employees,id",
            ],
            'position_title' => [
                "required",
                "string"
            ],
            'employment_status' => [
                "required",
                "string",
                new Enum(EmploymentType::class)
            ],
            'department_id' => [
                "required",
                "integer",
                "exists:departments,id"
            ],
            'immediate_supervisor' => [
                "required",
                "string"
            ],
            'salary_grades' => [
                "nullable",
                "integer",
                "exists:salary_grade_steps,id",
            ],
            'salary_type' => [
                "nullable",
                "string",
                new Enum(SalaryRequestType::class)
            ],
            'actual_salary' => [
                "nullable",
                "string"
            ],
            'work_location' => [
                "required",
                "string",
                'in:Office,Project Code'
            ],
            'hire_source' => [
                "required",
                "string",
                'in:Internal,External'
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

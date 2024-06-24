<?php

namespace App\Http\Requests;

use App\Enums\AccessibilityHrms;
use App\Enums\SalaryRequestType;
use App\Http\Traits\CheckAccessibility;
use App\Models\Accessibilities;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateInternalWorkExperienceRequest extends FormRequest
{
    use CheckAccessibility;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return true; // Temp for ID Deployment
        return $this->checkUserAccess([AccessibilityHrms::HRMS_EMPLOYEE_201_EDIT->value]);
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
            'position_id' => [
                "nullable",
                "integer",
                "exists:positions,id",
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
                'in:Office,Project Code'
            ],
            'hire_source' => [
                "nullable",
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
            'salary_type' => [
                "nullable",
                "string",
                new Enum(SalaryRequestType::class)
            ],
        ];
    }
}

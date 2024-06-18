<?php

namespace App\Http\Requests;

use App\Enums\PersonelAccessForm;
use App\Enums\SalaryRequestType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;

class UpdateInternalWorkExperienceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return in_array(Auth::user()->id, config('app.201_editor'));
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

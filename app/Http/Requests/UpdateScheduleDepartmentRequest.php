<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateScheduleDepartmentRequest extends FormRequest
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
            'groupType' => [
                "nullable",
                "string",
                "in:department,project,employee"
            ],
            'department_id' => [
                "nullable",
                "integer",
                "exists:departments,id",
                'nullable_if:groupType,==,department',
            ],
            'project_id' => [
                "nullable",
                "integer",
                'nullable_if:groupType,==,project',
            ],
            'employee_id' => [
                "nullable",
                "integer",
                "exists:employees,id",
                'nullable_if:groupType,==,employee',
            ],
            'scheduleType' => [
                "nullable",
                "string",
                "in:Regular, Irregular",
            ],
            'daysOfWeek' => [
                "nullable",
                "array",
            ],
            'daysOfWeek.*' => [
                "nullable",
                "integer",
                "min:0",
                "max:6",
            ],
            'startTime' => [
                "nullable",
                "date_format:H:i'",
            ],
            'endTime' => [
                "nullable",
                "date_format:H:i|after:startTime",
            ],
            'startRecur' => [
                "nullable",
                "date",
            ],
            'endRecur' => [
                "nullable",
                "date",
                "after_or_equal:startRecur"
            ],
        ];
    }
}

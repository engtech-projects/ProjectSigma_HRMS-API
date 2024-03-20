<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreScheduleDepartmentRequest extends FormRequest
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
                "required",
                "string",
                "in:department,project,employee"
            ],
            'department_id' => [
                "nullable",
                "integer",
                "exists:departments,id",
                'required_if:groupType,==,department',
            ],
            'project_id' => [
                "nullable",
                "integer",
                'required_if:groupType,==,project',
            ],
            'employee_id' => [
                "nullable",
                "integer",
                "exists:employees,id",
                'required_if:groupType,==,employee',
            ],
            'scheduleType' => [
                "required",
                "string",
                "in:Regular, Irregular",
            ],
            'daysOfWeek' => [
                "required",
                "array",
            ],
            'daysOfWeek.*' => [
                "required",
                "integer",
                "min:0",
                "max:6",
            ],
            'startTime' => [
                "required",
                "date_format:H:i:s'",
            ],
            'endTime' => [
                "required",
                "date_format:H:i:s|after:startTime",
            ],
            'startRecur' => [
                "required",
                "date",
            ],
            'endRecur' => [
                "required",
                "date",
                "after_or_equal:startRecur"
            ],
        ];
    }
}

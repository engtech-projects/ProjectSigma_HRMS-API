<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreScheduleRequest extends FormRequest
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
                "exclude_if:groupType,project,employee",
            ],
            'project_id' => [
                "nullable",
                "integer",
                'required_if:groupType,==,project',
                "exclude_if:groupType,department,employee",
            ],
            'employee_id' => [
                "nullable",
                "integer",
                "exists:employees,id",
                'required_if:groupType,==,employee',
                "exclude_if:groupType,department,project",
            ],
            'scheduleType' => [
                "required",
                "string",
                "in:Regular,Irregular",
            ],
            'daysOfWeek' => [
                "nullable",
                "array",
                "required_if:scheduleType,==,Regular",
                "exclude_if:scheduleType,!=,Regular"
            ],
            'daysOfWeek.*' => [
                "required",
                "integer",
                "min:0",
                "max:6",
            ],
            'startTime' => [
                "required",
                "date_format:H:i:s",
            ],
            'endTime' => [
                "required",
                "date_format:H:i:s",
                "after:startTime",
            ],
            'startRecur' => [
                "required",
                "date_format:Y-m-d",
            ],
            'endRecur' => [
                "required",
                "date_format:Y-m-d",
                "after_or_equal:startRecur"
            ],
        ];
    }
}

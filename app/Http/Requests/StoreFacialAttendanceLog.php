<?php

namespace App\Http\Requests;

use App\Enums\AssignTypes;
use App\Enums\AttendanceLogType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreFacialAttendanceLog extends FormRequest
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
                "required",
                "integer",
                "exists:employees,id",
            ],
            'project_id' => [
                'required_if:group_type,==,Project',
                "integer",
                "exists:projects,id",
            ],
            'department_id' => [
                'required_if:group_type,==,Department',
                "integer",
                "exists:departments,id",
            ],
            'log_type' => [
                "required",
                "string",
                new Enum(AttendanceLogType::class)
            ],
            'group_type' => [
                "required",
                "string",
                new Enum(AssignTypes::class)
            ],
        ];
    }
}

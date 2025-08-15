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
            'log_type' => [
                "required",
                "string",
                new Enum(AttendanceLogType::class)
            ],
            // WHEN TYPE IS PROJECT THE SPECIFIED project_id WILL BE REQUIRED AND LOGGED IN THE ATTENDANCE AS CHARGED
            // WHEN TYPE IS DEPARTMENT THE SPECIFIED department_id WILL BE A PLACEHOLDER AS A LAST RESORT INCASE THE EMPLOYEE DOESN'T HAVE A DEPARTMENT OR PROJECT
            'assignment_type' => [
                "required",
                "string",
                new Enum(AssignTypes::class)
            ],
            'project_id' => [
                'nullable',
                "integer",
                "exists:projects,id",
                "required_if:assignment_type,==," . AssignTypes::PROJECT->value,
            ],
            'department_id' => [
                'nullable',
                "integer",
                "exists:departments,id",
            ],
        ];
    }
}

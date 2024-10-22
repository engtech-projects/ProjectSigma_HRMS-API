<?php

namespace App\Http\Requests;

use App\Enums\AssignTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreAttendancePortalRequest extends FormRequest
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
            'name_location' => [
                "required",
                "string",
            ],
            'assignments' => [
                "required",
                "array",
                "min:1",
                "max:10",
            ],
            'assignments.*.assignment_type' => [
                "required",
                "string",
                new Enum(AssignTypes::class),
            ],
            'assignments.*.project_id' => [
                'required_if:assignments.*.assignment_type,==,' . AssignTypes::PROJECT->value,
                'nullable',
                "integer",
                "exists:projects,id",
            ],
            'assignments.*.department_id' => [
                'required_if:assignments.*.assignment_type,==,' . AssignTypes::DEPARTMENT->value,
                'nullable',
                "integer",
                "exists:departments,id",
            ],
        ];
    }
}

<?php

namespace App\Http\Requests;

use App\Enums\AttendanceLogType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreQrAttendanceLog extends FormRequest
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
            'department_id' => [
                'nullable',
                'numeric'
            ],
            'offset' => [
                'required',
                'numeric',
            ],
            'project_id' => [
                'nullable',
                'numeric'
            ],
            'employee_code' => [
                "required",
                "string",
                "exists:company_employments,employeedisplay_id",
            ],
            'log_type' => [
                "required",
                "string",
                new Enum(AttendanceLogType::class)
            ],
        ];
    }
}

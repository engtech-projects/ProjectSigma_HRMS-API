<?php

namespace App\Http\Requests;

use App\Enums\AttendanceLogType;
use App\Enums\AttendanceType;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceLogRequest extends FormRequest
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
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|date_format:H:i:s',
            'log_type' => [
                'required',
                'string',
                new Enum(AttendanceLogType::class)
            ],
            'attendance_type' => [
                'required',
                'string',
                new Enum(AttendanceType::class)
            ],
            'project_id'  => 'required|integer',
            'department_id' => 'required|integer',
        ];
    }
}

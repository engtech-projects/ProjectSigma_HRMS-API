<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AllAttendanceLogsRequest extends FormRequest
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
            'employee_id' => 'nullable|numeric',
            'date' => 'nullable|date',
            'project_id' => 'nullable|numeric|exists:projects,id',
            'department_id' => 'nullable|numeric|exists:departments,id',
            'attendance_type' => 'nullable|string',
        ];
    }
}

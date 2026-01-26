<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if (gettype($this->attendance_data) == "string") {
            $this->merge([
                "attendance_data" => json_decode($this->attendance_data, true)
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "attendance_data" => ['array', 'present'],
            "attendance_data.*" => 'present',
            "attendance_data.*.project" => 'present',
            "attendance_data.*.project_id" => 'present',
            "attendance_data.*.employee_id" => 'present',
            "attendance_data.*.first_name" => 'present',
            "attendance_data.*.middle_name" => 'present',
            "attendance_data.*.family_name" => 'present',
            "attendance_data.*.date" => 'present',
            "attendance_data.*.time_in" => 'present',
            "attendance_data.*.time_out" => 'present',
        ];
    }
}

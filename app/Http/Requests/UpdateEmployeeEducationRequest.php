<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeEducationRequest extends FormRequest
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
                "nullable",
                "integer",
                "exists:employees,id",
            ],
            'type' => [
                "nullable",
                "string",
                "in:elementary,secondary,vocational_course,college,graduate_studies",
            ],
            'name' => [
                "nullable",
                "string",
            ],
            'education' => [
                "nullable",
                "string",
            ],
            'period_attendance_to' => [
                "nullable",
                "string",
            ],
            'period_attendance_from' => [
                "nullable",
                "string",
            ],
            'year_graduated' => [
                "nullable",
                "string",
            ],
            'degree_earned_of_school' => [
                "nullable",
                "string",
            ],
            'honors_received' => [
                "nullable",
                "string",
            ],
        ];
    }
}

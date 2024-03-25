<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeEducationRequest extends FormRequest
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
            'employee_id'=> [
                "required",
                "integer",
                "exists:employees,id",
            ],
            'type'=>[
                "required",
                "string",
                "in:elementary,secondary,vocational_course,college,graduate_studies",
            ],
            'name'=>[
                "required",
                "string",
            ],
            'education'=>[
                "required",
                "string",
            ],
            'period_attendance_to'=>[
                "required",
                "string",
            ],
            'period_attendance_from'=>[
                "required",
                "string",
            ],
            'year_graduated'=>[
                "required",
                "string",
            ],
            'degree_earned_of_school'=>[
                "required",
                "string",
            ],
            'honors_received'=>[
                "required",
                "string",
            ],
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExternalWorkExperienceRequest extends FormRequest
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
            'application_name' => [
                "required",
                'string'
            ],
            'position_title' => [
                "required",
                'string'
            ],
            'company_name' => [
                "required",
                "string",
            ],
            'salary' => [
                "required",
                "integer",
                "min:0"
            ],
            'status_of_appointment' => [
                "required",
                "string",
            ],
            'date_from' => [
                "required",
                "date",
            ],
            'date_to' => [
                "required",
                "date",
            ],
        ];
    }
}

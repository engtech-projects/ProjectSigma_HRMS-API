<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRecordRequest extends FormRequest
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
            'date_from' => [
                "required",
                "date",
            ],
            'date_to' => [
                "required",
                "date",
                "after:date_from"
            ],
            'position_title' => [
                "required",
                "string",
            ],
            'company_name' => [
                "required",
                "string",
            ],
            'monthly_salary' => [
                "required",
                "string",
            ],
            'status_of_appointment' => [
                "required",
                "string",
            ],
        ];
    }
}

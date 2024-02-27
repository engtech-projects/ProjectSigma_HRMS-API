<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRecordRequest extends FormRequest
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
                "nullable",
                "integer",
                "exists:employees,id",
            ],
            'date_to'=>[
                "nullable",
                "date",
            ],
            'date_from'=>[
                "nullable",
                "date",
            ],
            'position_title'=>[
                "nullable",
                "string",
            ],
            'company_name'=>[
                "nullable",
                "string",
            ],
            'monthly_salary'=>[
                "nullable",
                "string",
            ],
            'status_of_appointment'=>[
                "nullable",
                "string",
            ],
        ];
    }
}

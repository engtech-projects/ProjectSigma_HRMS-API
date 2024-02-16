<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyEmployeeRequest extends FormRequest
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
            'employeedisplay_id'=> [
                "nullable",
                "string",
            ],
            'company'=>[
                "nullable",
                "string",
            ],
            'date_hired'=>[
                "nullable",
                "date",
            ],
            'imidiate_supervisor'=>[
                "nullable",
                "string",
            ],
            'phic_number'=>[
                "nullable",
                "string",
            ],
            'sss_number'=>[
                "nullable",
                "string",
            ],
            'tin_number'=>[
                "nullable",
                "string",
            ],
            'pagibig_number'=>[
                "nullable",
                "string",
            ],
        ];
    }
}

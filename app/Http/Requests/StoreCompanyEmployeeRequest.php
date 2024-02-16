<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyEmployeeRequest extends FormRequest
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
            'employeedisplay_id'=> [
                "required",
                "string",
            ],
            'company'=>[
                "required",
                "string",
            ],
            'date_hired'=>[
                "required",
                "date",
            ],
            'imidiate_supervisor'=>[
                "required",
                "string",
            ],
            'phic_number'=>[
                "required",
                "string",
            ],
            'sss_number'=>[
                "required",
                "string",
            ],
            'tin_number'=>[
                "required",
                "string",
            ],
            'pagibig_number'=>[
                "required",
                "string",
            ],
        ];
    }
}

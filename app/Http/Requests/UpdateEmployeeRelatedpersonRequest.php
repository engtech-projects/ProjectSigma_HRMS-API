<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRelatedpersonRequest extends FormRequest
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
            //
            'employee_id'=> [
                "nullable",
                "integer",
                "exists:employees,id",
            ],
            'relationship'=>[
                "nullable",
                "string",
            ],
            'type'=>[
                "nullable",
                "string",
                'in:contact person,dependent/children,father,mother,spouse,reference'
            ],
            'name'=>[
                "nullable",
                "string",
            ],
            'date_of_birth'=>[
                "nullable",
                "date",
            ],
            'street'=>[
                "nullable",
                "string",
            ],
            'brgy'=>[
                "nullable",
                "string",
            ],
            'city'=>[
                "nullable",
                "string",
            ],
            'zip'=>[
                "nullable",
                "string",
            ],
            'province'=>[
                "nullable",
                "string",
            ],
            'occupation'=>[
                "nullable",
                "string",
            ],
            'contact_no'=>[
                "nullable",
                "string",
            ],
        ];
    }
}

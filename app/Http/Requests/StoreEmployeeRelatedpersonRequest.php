<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRelatedpersonRequest extends FormRequest
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
            'relationship'=>[
                "required",
                "string",
            ],
            'type'=>[
                "required",
                "string",
                'in:contact person,dependent/children,father,mother,spouse,reference'
            ],
            'name'=>[
                "required",
                "string",
            ],
            'date_of_birth'=>[
                "required",
                "date",
            ],
            'street'=>[
                "required",
                "string",
            ],
            'brgy'=>[
                "required",
                "string",
            ],
            'city'=>[
                "required",
                "string",
            ],
            'zip'=>[
                "required",
                "string",
            ],
            'province'=>[
                "required",
                "string",
            ],
            'occupation'=>[
                "required",
                "string",
            ],
            'contact_no'=>[
                "required",
                "string",
            ],
        ];
    }
}

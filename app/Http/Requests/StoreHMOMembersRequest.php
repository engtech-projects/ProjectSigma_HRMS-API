<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHMOMembersRequest extends FormRequest
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
            return [
                'hmo_id'=> [
                    "required",
                    "integer",
                    "exists:hmo,id",
                ],
                'member_type'=> [
                    "required",
                    "string",
                    'in:employee,external(addon)'
                ],
                'employee_id'=> [
                    "required",
                    "integer",
                    "exists:employees,id",
                ],
                'member_name'=> [
                    "required",
                    "string",
                ],
                'member_belongs_to'=> [
                    "required",
                    "integer",
                    "exists:employees,id",
                ],
            ];
        ]
    }
}

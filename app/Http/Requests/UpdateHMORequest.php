<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHMORequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            "hmo_members" => json_decode($this->hmo_members, true)
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'hmo_name' => [
                "nullable",
                "string",
            ],
            'hmo_start' => [
                "nullable",
                "date",
                "date_format:Y-m-d",
            ],
            'hmo_end' => [
                "nullable",
                "date",
                "date_format:Y-m-d",
            ],
            'employee_share' => [
                "nullable",
                "numeric",
                "min:0",
                'max:999999',
                'decimal:0,2',
            ],
            'employer_share' => [
                "nullable",
                "numeric",
                "min:0",
                'max:999999',
                'decimal:0,2',
            ],
            'hmo_members' => [
                "nullable",
                "array",
            ],
            'hmo_members.*' => [
                "nullable",
                "array",
                "nullable_array_keys:member_type,employee_id,member_name,member_belongs_to",
            ],
            'hmo_members.*.hmo_id' => [
                "nullable",
                "integer",
            ],
            'hmo_members.*.member_type' => [
                "nullable",
                "string",
                'in:employee,external(addon)'
            ],
            'hmo_members.*.employee_id' => [
                "nullable",
                "integer",
                "exists:employees,id",
            ],
            'hmo_members.*.member_name' => [
                "nullable",
                "string",
            ],
            'hmo_members.*.member_belongs_to' => [
                "integer",
                "exists:employees,id",
            ],
        ];
    }
}

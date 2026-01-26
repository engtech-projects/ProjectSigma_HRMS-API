<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHMORequest extends FormRequest
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
        if (gettype($this->hmo_members) == "string") {
            $this->merge([
                "hmo_members" => json_decode($this->hmo_members, true)
            ]);
        }
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
                "required",
                "string",
            ],
            'hmo_start' => [
                "required",
                "date",
                "date_format:Y-m-d",
            ],
            'hmo_end' => [
                "required",
                "date",
                "date_format:Y-m-d",
            ],
            'employee_share' => [
                "required",
                "numeric",
                "min:0",
                'max:999999',
                'decimal:0,2',
            ],
            'employer_share' => [
                "required",
                "numeric",
                "min:0",
                'max:999999',
                'decimal:0,2',
            ],
            'hmo_members' => [
                "required",
                "array",
            ],
            'hmo_members.*' => [
                "required",
                "array",
            ],
            'hmo_members.*.member_type' => [
                "required",
                "string",
                'in:employee,external(addon)'
            ],
            'hmo_members.*.employee_id' => [
                "nullable",
                "integer",
                "exists:employees,id",
                'required_if:hmo_members.*.member_type,employee',
            ],
            'hmo_members.*.member_name' => [
                "required",
                "string",
            ],
            'hmo_members.*.member_belongs_to' => [
                "nullable",
                "integer",
                "exists:employees,id",
                'required_if:hmo_members.*.member_type,external(addon)',
            ],
        ];
    }
}

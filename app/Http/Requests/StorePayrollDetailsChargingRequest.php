<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StorePayrollDetailsChargingRequest extends FormRequest
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
            'name' => [
                "required",
                "string",
            ],
            'payroll_details_id' => [
                "required",
                "integer",
                "exists:payroll_details",
            ],
            'amount' => [
                "required",
                "numeric",
                "min:1",
                'decimal:0,2',
            ],
            'group_type' => [
                "required",
                "string",
                new Enum(AssignTypes::class)
            ],
            'project_id' => [
                'required_if:group_type,==,' . AssignTypes::PROJECT->value,
                'nullable',
                "integer",
                "exists:projects,id",
            ],
            'department_id' => [
                'required_if:group_type,==,' . AssignTypes::DEPARTMENT->value,
                'nullable',
                "integer",
                "exists:departments,id",
            ],
        ];
    }
}

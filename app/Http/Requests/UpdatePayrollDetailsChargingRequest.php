<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdatePayrollDetailsChargingRequest extends FormRequest
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
                "nullable",
                "string",
            ],
            'payroll_details_id' => [
                "nullable",
                "integer",
                "exists:payroll_details",
            ],
            'amount' => [
                "nullable",
                "numeric",
                "min:1",
                'decimal:0,2',
            ],
            'group_type' => [
                "nullable",
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

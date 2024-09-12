<?php

namespace App\Http\Requests;

use App\Enums\AssignTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Http\Traits\HasApprovalValidation;

class StoreAllowanceRequestRequest extends FormRequest
{
    use HasApprovalValidation;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if (gettype($this->employee_allowances) == "string") {
            $this->merge([
                "employee_allowances" => json_decode($this->employee_allowances, true),
            ]);
        }
        $this->prepareApprovalValidation();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'charging_type' => [
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
            'allowance_date' => [
                "required",
                "date",
                'date_format:Y-m-d'
            ],
            'cutoff_start' => [
                "required",
                "date",
                'date_format:Y-m-d'
            ],
            'cutoff_end' => [
                "required",
                "date",
                'date_format:Y-m-d'
            ],
            'total_days' => [
                "required",
                "numeric",
                "min:1",
                'decimal:0,2',
            ],
            ...$this->storeApprovals(),
            'employee_allowances' => [
                "required",
                "array",
            ],
            'employee_allowances.*' => [
                "required",
                "array",
            ],
            'employee_allowances.*.allowance_amount' => [
                "required",
                "numeric",
                "min:1",
                'decimal:0,2',
            ],
            'employee_allowances.*.employee_id' => [
                "required",
                "integer",
                "exists:employees,id",
            ],
            'employee_allowances.*.allowance_rate' => [
                "required",
                "numeric",
                "min:1",
                'decimal:0,2',
            ],
            'employee_allowances.*.allowance_days' => [
                "required",
                "numeric",
                "min:1",
            ],
        ];
    }
}

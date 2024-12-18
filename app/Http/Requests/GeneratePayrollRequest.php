<?php

namespace App\Http\Requests;

use App\Enums\AssignTypes;
use App\Enums\PayrollType;
use App\Enums\ReleaseType;
use App\Http\Traits\HasApprovalValidation;
use App\Enums\PayrollDetailsDeductionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class GeneratePayrollRequest extends FormRequest
{
    use HasApprovalValidation;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        $this->prepareApprovalValidation();
        if (gettype($this->employee_ids) == "string") {
            $this->merge([
                'employee_ids' => json_decode($this->employee_ids, true),
            ]);
        }
        if (gettype($this->adjustments) == "string") {
            $this->merge([
                'adjustments' => json_decode($this->adjustments, true),
            ]);
        }
        if (gettype($this->chargings) == "string") {
            $this->merge([
                'chargings' => json_decode($this->chargings, true),
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
            // GENERATE AND STORE SAME FIELDS
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
            'payroll_type' => [
                'required',
                'string',
                new Enum(PayrollType::class)
            ],
            'release_type' => [
                'required',
                'string',
                new Enum(ReleaseType::class)
            ],
            'payroll_date' => 'required|date_format:Y-m-d',
            'cutoff_start' => 'required|date_format:Y-m-d',
            'cutoff_end' => 'required|date_format:Y-m-d',
            'advance_days' => [
                "sometimes",
                "integer",
                "min:0",
            ],
            ...$this->storeApprovals(),
            // GENERATE SPECIFIC
            'employee_ids' => 'required|array',
            'deduct_sss' => 'required|boolean',
            'deduct_philhealth' => 'required|boolean',
            'deduct_pagibig' => 'required|boolean',
            'adjustments' => 'nullable|array',
            'adjustments.*' => [
                "required",
                "array",
            ],
            'adjustments.*.employee_id' => [
                "required",
                "integer",
                "exists:employees,id",
            ],
            'adjustments.*.adjustment_name' => [
                "required",
                "string",
            ],
            'adjustments.*.adjustment_amount' => [
                "required",
                "numeric",
                'max:999999',
                "min:0",
                'decimal:0,2',
            ],
        ];
    }
}

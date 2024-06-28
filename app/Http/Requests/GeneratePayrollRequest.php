<?php

namespace App\Http\Requests;

use App\Enums\AssignTypes;
use App\Enums\PayrollType;
use App\Enums\ReleaseType;
use App\Enums\RequestApprovalStatus;
use App\Enums\StringRequestApprovalStatus;
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
            'group_type' => [
                'required',
                new Enum(AssignTypes::class)
            ],
            'project_id' => 'required_if:group_type,project|integer|nullable',
            'department_id' => 'required_if:group_type,department|integer|nullable',
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
            'employee_ids' => 'required|array',
            'deduct_sss' => 'required|boolean',
            'deduct_philhealth' => 'required|boolean',
            'deduct_pagibig' => 'required|boolean',
            ...$this->storeApprovals(),
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
            'chargings' => 'nullable|array',
            'chargings.*' => [
                "required",
                "array",
            ],
            'chargings.*.name' => [
                "required",
                "string",
            ],
            'chargings.*.amount' => [
                "required",
                "numeric",
                "min:0",
                'decimal:0,2',
            ],
            'chargings.*.charge_id' => [
                "required",
                "integer",
            ],
            'chargings.*.type' => [
                "required",
                "string",
                new Enum(PayrollDetailsDeductionType::class)
            ],
        ];
    }
}

<?php

namespace App\Http\Requests;

use App\Enums\GroupType;
use App\Enums\ReleaseType;
use App\Http\Traits\HasApprovalValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StorePayrollRecordRequest extends FormRequest
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
        $this->prepareApprovalValidation();
        $this->merge([
            "payroll_details" => json_decode($this->payroll_details, true)
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
            'group_type' => [
                'required',
                new Enum(GroupType::class)
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
            'payroll_details' => 'required|array',
            // deductions = polymorp(Cash Advance ,Loan ,Other Deduction ,Others)  type, deduction_type, deduction_id
            'payroll_details.*.deductions' => 'required|array',
            'payroll_details.*.deductions.*.name' => 'required|string',
            'payroll_details.*.deductions.*.amount' => [
                "required",
                "numeric",
                "min:1",
                'decimal:0,2',
            ],
            'payroll_details.*.deductions.*.payroll_details_id' => [
                "required",
                "integer",
                "exists:payroll_details,id",
            ],
            // adjustments = name,amount
            'payroll_details.*.adjustment' => 'required|array',
            'payroll_details.*.adjustment.*.payroll_details_id' => [
                "required",
                "integer",
                "exists:payroll_details,id",
            ],
            'payroll_details.*.adjustment.*.name' => 'required|string',
            'payroll_details.*.adjustment.*.amount' => [
                "required",
                "numeric",
                "min:1",
                'decimal:0,2',
            ],

            // chargings = polymorp(Cash Advance ,Loan ,Other Deduction ,Others)  name, amount, charge_type, charge_id
            'payroll_details.*.charging' => 'required|array',
            'payroll_details.*.charging.*.name' => 'required|string',
            'payroll_details.*.charging.*.amount' => [
                "required",
                "numeric",
                "min:1",
                'decimal:0,2',
            ],
            'payroll_details.*.charging.*.payroll_details_id' => [
                "required",
                "integer",
                "exists:payroll_details,id",
            ],

            'payroll_date' => 'required|date_format:Y-m-d',
            'cutoff_start' => 'required|date_format:Y-m-d',
            'cutoff_end' => 'required|date_format:Y-m-d',
            'deduct_sss' => 'required|boolean',
            'deduct_philhealth' => 'required|boolean',
            'deduct_pagibig' => 'required|boolean',
            ...$this->storeApprovals(),
        ];
    }
}

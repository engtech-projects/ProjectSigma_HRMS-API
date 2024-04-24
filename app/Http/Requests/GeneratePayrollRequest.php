<?php

namespace App\Http\Requests;

use App\Enums\GroupType;
use App\Enums\ReleaseType;
use App\Enums\RequestApprovalStatus;
use App\Enums\StringRequestApprovalStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class GeneratePayrollRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        $this->merge([
            'employee_ids' => json_decode($this->employee_ids, true),
            'approvals' => json_decode($this->approvals, true)
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
            'project_id' => 'required|integer',
            'department_id' => 'required|integer',
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
            'approvals' => 'required|array',
            'approvals.*' => 'required|array|required_array_keys:type,user_id,status',
            'approvals.*.type' => 'required|string',
            'approvals.*.user_id' => 'required|integer',
            'approvals.*.date_approved' => 'date_format:Y-m-d|nullable',
            'approvals.*.remarks' => 'string|nullable',
            'approvals.*.status' => [
                'required',
                'string',
                new Enum(StringRequestApprovalStatus::class)
            ]

        ];
    }
}

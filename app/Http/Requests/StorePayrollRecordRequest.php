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
            'payroll' => 'required|array',
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

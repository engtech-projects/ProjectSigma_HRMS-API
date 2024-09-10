<?php

namespace App\Http\Requests;

use App\Enums\AccessibilityHrms;
use App\Enums\AssignTypes;
use App\Enums\PayrollType;
use App\Enums\ReleaseType;
use App\Http\Traits\CheckAccessibility;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class PayrollRecordsRequest extends FormRequest
{
    use CheckAccessibility;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->checkUserAccess([AccessibilityHrms::HRMS_PAYROLL_SALARY_PAYROLLRECORD->value]);
    }

    protected function prepareForValidation()
    {
        if ($this->release_type == "all") {
            $this->merge([
                "release_type" => null
            ]);
        }
        if ($this->payroll_type == "all") {
            $this->merge([
                "payroll_type" => null
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
            "payroll_date" => [
                "date",
                "required",
            ],
            "release_type" => [
                "nullable",
                "string",
                "max:200",
                new Enum(ReleaseType::class),
            ],
            "payroll_type" => [
                "nullable",
                "string",
                "max:200",
                new Enum(PayrollType::class),
            ],
            'charging_type' => [
                'nullable',
                new Enum(AssignTypes::class)
            ],
            'project_id' => 'required_if:charging_type,project|integer|nullable',
            'department_id' => 'required_if:charging_type,department|integer|nullable',
        ];
    }
}

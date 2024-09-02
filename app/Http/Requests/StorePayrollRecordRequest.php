<?php

namespace App\Http\Requests;

use App\Enums\PayrollDetailsDeductionType;
use App\Enums\AssignTypes;
use App\Enums\ReleaseType;
use App\Enums\PayrollType;
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
        if (gettype($this->payroll_details) == "string") {
            $this->merge([
                "payroll_details" => json_decode($this->payroll_details, true)
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
            ...$this->payrollDetails(),
            ...$this->storeApprovals(),
        ];
    }

    public function payrollDetails(): array
    {
        return [
            //payroll details
            'payroll_details' => 'required|array',
            'payroll_details.*' => 'required|array',
            'payroll_details.*.employee_id' => [
                "required",
                "integer",
                "exists:employees,id",
            ],
            'payroll_details.*.regular_hours' => [
                "required",
                "numeric",
                "min:0",
                'decimal:0,2',
            ],
            'payroll_details.*.rest_hours' => [
                "required",
                "numeric",
                "min:0",
                'decimal:0,2',
            ],
            'payroll_details.*.regular_holiday_hours' => [
                "required",
                "numeric",
                "min:0",
                'decimal:0,2',
            ],
            'payroll_details.*.special_holiday_hours' => [
                "required",
                "numeric",
                "min:0",
                'decimal:0,2',
            ],
            'payroll_details.*.regular_overtime' => [
                "required",
                "numeric",
                "min:0",
                'decimal:0,2',
            ],
            'payroll_details.*.rest_overtime' => [
                "required",
                "numeric",
                "min:0",
                'decimal:0,2',
            ],
            'payroll_details.*.regular_holiday_overtime' => [
                "required",
                "numeric",
                "min:0",
                'decimal:0,2',
            ],
            'payroll_details.*.special_holiday_overtime' => [
                "required",
                "numeric",
                "min:0",
                'decimal:0,2',
            ],
            'payroll_details.*.regular_pay' => [
                "required",
                "numeric",
                "min:0",
                'decimal:0,2',
            ],
            'payroll_details.*.rest_pay' => [
                "required",
                "numeric",
                "min:0",
                'decimal:0,2',
            ],
            'payroll_details.*.regular_holiday_pay' => [
                "required",
                "numeric",
                "min:0",
                'decimal:0,2',
            ],
            'payroll_details.*.special_holiday_pay' => [
                "required",
                "numeric",
                "min:0",
                'decimal:0,2',
            ],
            'payroll_details.*.regular_ot_pay' => [
                "required",
                "numeric",
                "min:0",
                'decimal:0,2',
            ],
            'payroll_details.*.rest_ot_pay' => [
                "required",
                "numeric",
                "min:0",
                'decimal:0,2',
            ],
            'payroll_details.*.regular_holiday_ot_pay' => [
                "required",
                "numeric",
                "min:0",
                'decimal:0,2',
            ],
            'payroll_details.*.special_holiday_ot_pay' => [
                "required",
                "numeric",
                "min:0",
                'decimal:0,2',
            ],
            'payroll_details.*.gross_pay' => [
                "required",
                "numeric",
                "min:0",
                'decimal:0,2',
            ],
            'payroll_details.*.sss_employee_contribution' => [
                'nullable',
                "numeric",
                "min:0",
                'decimal:0,2',
            ],
            'payroll_details.*.sss_employer_contribution' => [
                'nullable',
                "numeric",
                "min:0",
                'decimal:0,2',
            ],
            'payroll_details.*.sss_employee_compensation' => [
                'nullable',
                "numeric",
                "min:0",
                'decimal:0,2',
            ],
            'payroll_details.*.sss_employer_compensation' => [
                'nullable',
                "numeric",
                "min:0",
                'decimal:0,2',
            ],
            'payroll_details.*.philhealth_employee_contribution' => [
                'nullable',
                "numeric",
                "min:0",
                'decimal:0,2',
            ],
            'payroll_details.*.philhealth_employer_contribution' => [
                'nullable',
                "numeric",
                "min:0",
                'decimal:0,2',
            ],
            'payroll_details.*.pagibig_employee_contribution' => [
                'nullable',
                "numeric",
                "min:0",
                'decimal:0,2',
            ],
            'payroll_details.*.pagibig_employer_contribution' => [
                'nullable',
                "numeric",
                "min:0",
                'decimal:0,2',
            ],
            'payroll_details.*.pagibig_employee_compensation' => [
                'nullable',
                "numeric",
                "min:0",
                'decimal:0,2',
            ],
            'payroll_details.*.pagibig_employer_compensation' => [
                'nullable',
                "numeric",
                "min:0",
                'decimal:0,2',
            ],
            'payroll_details.*.withholdingtax_contribution' => [
                'nullable',
                "numeric",
                "min:0",
                'decimal:0,2',
            ],
            'payroll_details.*.total_deduct' => [
                "numeric",
                "min:0",
                'decimal:0,2',
            ],
            'payroll_details.*.net_pay' => [
                "numeric",
                "min:0",
                'decimal:0,2',
            ],
            'payroll_details.*.deductions' => 'present|nullable|array',
            'payroll_details.*.deductions.*.deduction_id' => [
                "nullable",
                "integer",
            ],
            'payroll_details.*.deductions.*.name' => 'required|string',
            'payroll_details.*.deductions.*.amount' => [
                "required",
                "numeric",
                "min:0",
                'decimal:0,2',
            ],
            'payroll_details.*.deductions.*.type' => [
                "required",
                "string",
                new Enum(PayrollDetailsDeductionType::class)
            ],
            'payroll_details.*.adjustments' => 'present|nullable|array',
            'payroll_details.*.adjustments.*.name' => 'required|string',
            'payroll_details.*.adjustments.*.amount' => [
                "required",
                "numeric",
                "min:0",
                'decimal:0,2',
            ],
            'payroll_details.*.chargings' => 'required|array',
            'payroll_details.*.chargings.*.name' => 'required|string',
            'payroll_details.*.chargings.*.amount' => [
                "required",
                "numeric",
                "min:0",
                'decimal:0,2',
            ],
            'payroll_details.*.chargings.*.charge_id' => [
                "nullable",
                "integer",
            ],
            'payroll_details.*.chargings.*.charge_type' => [
                "required",
                "string",
            ],
        ];
    }

}

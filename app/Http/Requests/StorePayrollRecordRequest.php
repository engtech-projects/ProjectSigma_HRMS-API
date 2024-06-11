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
            'payroll_details.*.late_hours' => [
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
            // deductions = polymorp(Cash Advance ,Loan ,Other Deduction ,Others)  type, deduction_type, deduction_id
            'payroll_details.*.deductions' => 'required|array',
            'payroll_details.*.deductions.*.charge_id' =>[
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
            // adjustments = name,amount
            'payroll_details.*.adjustment' => 'required|array',
            'payroll_details.*.adjustment.*.name' => 'required|string',
            'payroll_details.*.adjustment.*.amount' => [
                "required",
                "numeric",
                "min:0",
                'decimal:0,2',
            ],
            // chargings = polymorp(Cash Advance ,Loan ,Other Deduction ,Others)  name, amount, charge_type, charge_id
            'payroll_details.*.charging' => 'required|array',
            'payroll_details.*.charging.*.name' => 'required|string',
            'payroll_details.*.charging.*.amount' => [
                "required",
                "numeric",
                "min:0",
                'decimal:0,2',
            ],
            'payroll_details.*.charging.*.charge_id' =>[
                "nullable",
                "integer",
            ],
            'payroll_details.*.charging.*.type' => [
                "required",
                "string",
                new Enum(PayrollDetailsDeductionType::class)
            ],
            ...$this->storeApprovals(),
        ];
    }
}

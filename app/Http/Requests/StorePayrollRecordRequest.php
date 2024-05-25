<?php

namespace App\Http\Requests;

use App\Http\Traits\HasApprovalValidation;
use Illuminate\Foundation\Http\FormRequest;

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
        /* [
                "payroll_record_id" => 1,
                "employee_id" => 1,
                "regular_hours" => 1,
                "rest_hours" => 1,
                "regular_holiday_hours" => 1,
                "special_holiday_hours" => 1,
                "regular_overtime" => 1,
                "rest_overtime" => 1,
                "regular_holiday_overtime" =>1,
                "special_holiday_overtime" =>1,
                "regular_pay" =>1,
                "rest_pay" =>1,
                "regular_holiday_pay" =>1,
                "special_holiday_pay" =>1,
                "regular_ot_pay" =>1,
                "rest_ot_pay" =>1,
                "regular_holiday_ot_pay" =>1,
                "special_holiday_ot_pay" =>1,
                "gross_pay" =>1,
                "late_hours" =>1,
                "sss_deduct" =>1,
                "philhealth_deduct" =>1,
                "pagibig_deduct" =>1,
                "withholdingtax_deduct" =>1,
                "total_deduct" =>1,
                "net_pay" =>1,

        ]; */
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
            'group_type' => 'required|string',
            'project_id' => 'required|integer',
            'department_id' => 'required|integer',
            'payroll_type' => 'required|string',
            'payroll_date' => 'required|date',
            'cutoff_start' => 'required|date',
            'cutoff_end' => 'required|date',
            ...$this->storeApprovals(),
        ];
    }
}

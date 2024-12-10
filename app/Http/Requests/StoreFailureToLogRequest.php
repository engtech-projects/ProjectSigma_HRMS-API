<?php

namespace App\Http\Requests;

use App\Enums\AssignTypes;
use App\Enums\AttendanceLogType;
use App\Http\Requests\Traits\PayrollLockValidationTrait;
use App\Http\Traits\HasApprovalValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreFailureToLogRequest extends FormRequest
{
    use HasApprovalValidation;
    use PayrollLockValidationTrait;
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
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'date' => [
                'required',
                'date_format:Y-m-d',
                function ($attribute, $value, $fail) {
                    if ($this->isPayrollLocked($this->date)) {
                        $fail("Payroll is locked for this Log date.");
                    }
                },
            ],
            'time' => 'required|date_format:H:i',
            'log_type' => [
                'required',
                'string',
                new Enum(AttendanceLogType::class)
            ],
            'reason' => 'required|string',
            'employee_id' => 'required|integer',
            'charging_type' => [
                'required',
                new Enum(AssignTypes::class)
            ],
            'project_id' => [
                'required_if:charging_type,==,' . AssignTypes::PROJECT->value,
                'nullable',
                "integer",
                "exists:projects,id",
            ],
            'department_id' => [
                'required_if:charging_type,==,' . AssignTypes::DEPARTMENT->value,
                'nullable',
                "integer",
                "exists:departments,id",
            ],
            ...$this->storeApprovals(),
        ];
    }
}

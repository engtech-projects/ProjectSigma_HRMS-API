<?php

namespace App\Http\Requests;

use App\Enums\AssignTypes;
use App\Http\Requests\Traits\PayrollLockValidationTrait;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Traits\HasApprovalValidation;
use Illuminate\Validation\Rules\Enum;

class StoreTravelOrderRequest extends FormRequest
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
        if (gettype($this->employee_ids) == "string") {
            $this->merge([
                "employee_ids" => json_decode($this->employee_ids, true)
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
            'charge_type' => [
                'required',
                new Enum(AssignTypes::class)
            ],
            'project_id' => [
                'required_if:charge_type,==,' . AssignTypes::PROJECT->value,
                'nullable',
                "integer",
                "exists:projects,id",
            ],
            'department_id' => [
                'required_if:charge_type,==,' . AssignTypes::DEPARTMENT->value,
                'nullable',
                "integer",
                "exists:departments,id",
            ],
            'requesting_office' => [
                "required",
                "integer",
                "exists:departments,id",
            ],
            'destination' => [
                "required",
                "string",
            ],
            'purpose_of_travel' => [
                "required",
                "string",
            ],
            'date_of_travel' => [
                "required",
                "date",
                function ($attribute, $value, $fail) {
                    if ($this->isPayrollLocked($this->date_of_travel)) {
                        $fail("Payroll is locked for this travel date.");
                    }
                },
            ],
            'time_of_travel' => [
                "required",
                "date_format:H:i",
            ],
            'duration_of_travel' => [
                "required",
                "numeric",
                "min:0",
                'decimal:0,2',
            ],
            'means_of_transportation' => [
                "required",
                "string",
            ],
            'remarks' => [
                "required",
                "string",
            ],
            'employee_ids' => [
                "required",
                "array",
            ],
            'employee_ids.*' => [
                "required",
                "integer",
                "exists:employees,id",
            ],
            ...$this->storeApprovals(),
        ];
    }
}

<?php

namespace App\Http\Requests;

use App\Enums\RequestStatusType;
use App\Enums\TermsOfPaymentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreCashAdvanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if (gettype($this->approvals) == "string") {
            $this->merge([
                "approvals" => json_decode($this->approvals, true)
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
            'employee_id' => [
                "required",
                "integer",
                "exists:employees,id",
            ],
            'department_id' => [
                "nullable",
                "integer",
                "exists:departments,id",
            ],
            'project_id' => [
                "nullable",
                "integer",
                "exists:projects,id",
            ],
            'amount' => [
                "required",
                "numeric",
                "min:1",
                'decimal:0,2',
            ],
            'terms_of_payment' => [
                "required",
                "string",
                new Enum(TermsOfPaymentType::class)
            ],
            'no_of_installment' => [
                "required",
                "integer",
                "min:1"
            ],
            'installment_deduction' => [
                "required",
                "numeric",
                "min:1",
                'decimal:0,2'
            ],
            'deduction_date_start' => [
                "required",
                "date",
                "date_format:Y-m-d",
            ],
            'purpose' => [
                "required",
                "string",
            ],
            'remarks' => [
                "required",
                "string",
            ],
            'approvals' => [
                "required",
                "array",
            ],
            'approvals.*' => [
                "required",
                "array",
            ],
            'approvals.*.type' => [
                "required",
                "string",
            ],
            'approvals.*.user_id' => [
                "nullable",
                "integer",
                "exists:users,id",
            ],
            'approvals.*.status' => [
                "required",
                "string",
            ],
            'approvals.*.date_approved' => [
                "nullable",
                "date",
            ],
            'approvals.*.remarks' => [
                "nullable",
                "string",
            ]
        ];
    }
}

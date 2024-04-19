<?php

namespace App\Http\Requests;

use App\Enums\RequestApprovalStatus;
use App\Enums\RequestStatusType;
use App\Enums\TermsOfPaymentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateCashAdvanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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
                "nullable",
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
                "nullable",
                "numeric",
                "min:1",
                'decimal:0,2',
            ],
            'terms_of_payment' => [
                "nullable",
                "string",
                new Enum(TermsOfPaymentType::class)
            ],
            'no_of_installment' => [
                "nullable",
                "integer",
                "min:1"
            ],
            'installment_deduction' => [
                "nullable",
                "numeric",
                "min:1",
                'decimal:0,2'
            ],
            'deduction_date_start' => [
                "nullable",
                "date",
                "date_format:Y-m-d",
            ],
            'purpose' => [
                "nullable",
                "string",
            ],
            'remarks' => [
                "nullable",
                "string",
            ],
            'approvals' => [
                "nullable",
                "array",
            ],
            'approvals.*' => [
                "nullable",
                "array",
            ],
            'approvals.*.type' => [
                "nullable",
                "string",
            ],
            'approvals.*.user_id' => [
                "nullable",
                "integer",
                "exists:users,id",
            ],
            'approvals.*.status' => [
                "nullable",
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

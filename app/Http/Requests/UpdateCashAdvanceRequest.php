<?php

namespace App\Http\Requests;

use App\Enums\RequestApprovalStatus;
use App\Enums\RequestStatusType;
use App\Enums\TermsOfPaymentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Http\Traits\ApprovalsRequest;

class UpdateCashAdvanceRequest extends FormRequest
{
    use ApprovalsRequest;
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
        $rules = [
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
        ];
        return array_merge($rules, $this->storeApprovals());
    }
}

<?php

namespace App\Http\Requests;

use App\Enums\LoanPaymentPostingStatusType;
use App\Enums\LoanPaymentsType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateOtherDeductionPaymentsRequest extends FormRequest
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
            'otherdeduction_id' => [
                "nullable",
                "integer",
                "exists:other_deductions,id",
            ],
            'amount_paid' => [
                "nullable",
                "numeric",
                "min:1",
                'decimal:0,2',
            ],
            'date_paid' => [
                "nullable",
                "date",
            ],
            'posting_status' => [
                "nullable",
                "string",
                new Enum(LoanPaymentPostingStatusType::class)
            ],
            'payment_type' => [
                "nullable",
                "string",
                new Enum(LoanPaymentsType::class)
            ],
        ];
    }
}

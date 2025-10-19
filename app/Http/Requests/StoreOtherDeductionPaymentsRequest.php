<?php

namespace App\Http\Requests;

use App\Enums\LoanPaymentPostingStatusType;
use App\Enums\LoanPaymentsType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreOtherDeductionPaymentsRequest extends FormRequest
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
                "required",
                "integer",
                "exists:other_deductions,id",
            ],
            'amount_paid' => [
                "required",
                "numeric",
                "min:1",
                'decimal:0,2',
            ],
            'date_paid' => [
                "required",
                "date",
            ],
            'posting_status' => [
                "required",
                "string",
                new Enum(LoanPaymentPostingStatusType::class)
            ],
            'payment_type' => [
                "required",
                "string",
                new Enum(LoanPaymentsType::class)
            ],
        ];
    }
}

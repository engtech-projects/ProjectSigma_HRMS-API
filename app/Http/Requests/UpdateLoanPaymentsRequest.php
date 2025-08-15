<?php

namespace App\Http\Requests;

use App\Enums\LoanPaymentPostingStatusType;
use App\Enums\LoanPaymentsType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateLoanPaymentsRequest extends FormRequest
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
            'loan_id' => [
                "nullable",
                "integer",
                "exists:loans,id",
            ],
            'amount_paid' => [
                "nullable",
                "numeric",
                'decimal:0,2',
                "min:1",
            ],
            'date_paid' => [
                "nullable",
                "date",
                "date_format:Y-m-d",
            ],
            'payment_type' => [
                "nullable",
                "string",
                new Enum(LoanPaymentsType::class)
            ],
            'posting_status' => [
                "nullable",
                "string",
                new Enum(LoanPaymentPostingStatusType::class)
            ],
        ];
    }
}

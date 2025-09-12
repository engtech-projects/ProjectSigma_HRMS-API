<?php

namespace App\Http\Requests;

use App\Enums\LoanPaymentPostingStatusType;
use App\Enums\LoanPaymentsType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreCashAdvancePaymentsRequest extends FormRequest
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
            'cashadvance_id' => [
                "required",
                "integer",
                "exists:cash_advances,id",
            ],
            'amount_paid' => [
                "required",
                "numeric",
                "min:1",
                "digits:2",
            ],
            'date_paid' => [
                "required",
                "numeric",
                "min:1",
                "digits:2",
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

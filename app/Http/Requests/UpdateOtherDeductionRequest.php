<?php

namespace App\Http\Requests;

use App\Enums\TermsOfPaymentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateOtherDeductionRequest extends FormRequest
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
            'employees' => [
                "nullable",
                "array",
            ],
            'employees.*' => [
                "nullable",
                "integer",
                "exists:employees,id",
            ],
            'otherdeduction_name' => [
                "nullable",
                "string",
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
            'no_of_installments' => [
                "nullable",
                "integer",
            ],
            'installment_deduction' => [
                "nullable",
                "numeric",
                "min:1",
                'decimal:0,2',
            ],
            'deduction_date_start' => [
                "nullable",
                "date",
                "date_format:Y-m-d",
            ],
        ];
    }
}

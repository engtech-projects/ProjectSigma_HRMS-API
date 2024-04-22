<?php

namespace App\Http\Requests;

use App\Enums\TermsOfPaymentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateLoansRequest extends FormRequest
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
            'amount' => [
                "nullable",
                "numeric",
                "min:1",
                'decimal:0,2',
            ],
            'installment_deduction' => [
                "nullable",
                "numeric",
                "min:1",
                'decimal:0,2',
            ],
            'terms_length' => [
                "nullable",
                "numeric",
                "min:1",
            ],
            'no_of_installment' => [
                "nullable",
                "numeric",
                "min:1",
            ],
            'deduction_date_start' => [
                "nullable",
                "date",
                "date_format:Y-m-d",
            ],
            'terms_of_payment' => [
                "nullable",
                "string",
                new Enum(TermsOfPaymentType::class)
            ],
        ];
    }
}

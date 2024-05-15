<?php

namespace App\Http\Requests;

use App\Enums\TermsOfPaymentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreLoansRequest extends FormRequest
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
                "required",
                "integer",
                "exists:employees,id",
            ],
            'amount' => [
                "required",
                "numeric",
                "min:1",
                'decimal:0,2',
            ],
            'installment_deduction' => [
                "required",
                "numeric",
                "min:1",
                'decimal:0,2',
            ],
            'deduction_date_start' => [
                "required",
                "date",
                "date_format:Y-m-d",
            ],
            'terms_of_payment' => [
                "required",
                "string",
                new Enum(TermsOfPaymentType::class)
            ],
        ];
    }
}

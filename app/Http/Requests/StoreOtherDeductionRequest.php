<?php

namespace App\Http\Requests;

use App\Enums\TermsOfPaymentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreOtherDeductionRequest extends FormRequest
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
            'otherdeduction_name' => [
                "required",
                "string",
            ],
            'total_amount' => [
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
            'no_of_installments' => [
                "required",
                "integer",
            ],
            'installment_amount' => [
                "required",
                "numeric",
                "min:1",
                'decimal:0,2',
            ],
        ];
    }
}

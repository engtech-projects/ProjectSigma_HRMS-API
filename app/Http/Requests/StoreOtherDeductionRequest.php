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

    protected function prepareForValidation()
    {
        if (gettype($this->employees) == "string") {
            $this->merge([
                "employees" => json_decode($this->employees, true)
            ]);
        }
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
                "required",
                "array",
            ],
            'employees.*' => [
                "required",
                "integer",
                "exists:employees,id",
            ],
            'otherdeduction_name' => [
                "required",
                "string",
            ],
            'amount' => [
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
        ];
    }
}

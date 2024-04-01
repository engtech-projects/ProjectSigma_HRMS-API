<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'loan_amount' => [
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
            'terms_length' => [
                "required",
                "numeric",
                "min:1",
            ],
            'period_start' => [
                "required",
                "date",
                "date_format:Y-m-d",
            ],
            'period_end' => [
                "required",
                "date",
                "date_format:Y-m-d",
                "after:period_start"
            ],
        ];
    }
}

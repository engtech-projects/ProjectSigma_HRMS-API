<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'loan_amount' => [
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
                "integer",
                "min:1",
            ],
            'period_start' => [
                "nullable",
                "date",
                "date_format:Y-m-d",
            ],
            'period_end' => [
                "nullable",
                "date",
                "date_format:Y-m-d",
                "after:period_start"
            ],
        ];
    }
}

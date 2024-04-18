<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWitholdingTaxContributionRequest extends FormRequest
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
            'range_from' => [
                "required",
                "numeric",
                'max:999999',
                'decimal:0,2',
            ],
            'range_to' => [
                "required",
                "numeric",
                'max:999999',
                'decimal:0,2',
            ],
            'term' => [
                "required",
                "in:Daily,Weekly,Semi-Monthly,Monthly",
            ],
            'tax_base' => [
                "required",
                "numeric",
                'max:999999',
                'decimal:0,2',
            ],
            'tax_amount' => [
                "required",
                "numeric",
                'max:999999',
                'decimal:0,2',
            ],
            'tax_percent_over_base' => [
                "required",
                "numeric",
                'max:999999',
                'decimal:0,2',
            ],
        ];
    }
}

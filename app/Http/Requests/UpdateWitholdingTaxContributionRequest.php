<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWitholdingTaxContributionRequest extends FormRequest
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
            'range_from'=> [
                "nullable",
                "numeric",
                'digits_between:1,8',
                'decimal:0,2',
            ],
            'range_to'=>[
                "nullable",
                "numeric",
                'digits_between:1,8',
                'decimal:0,2',
            ],
            'term'=>[
                "nullable",
                "in:Daily,Weekly,Semi-Monthly,Monthly",
            ],
            'tax_base'=>[
                "nullable",
                "numeric",
                'digits_between:1,8',
                'decimal:0,2',
            ],
            'tax_amount'=>[
                "nullable",
                "numeric",
                'digits_between:1,8',
                'decimal:0,2',
            ],
            'tax_percent_over_base'=>[
                "nullable",
                "numeric",
                'digits_between:1,8',
                'decimal:0,2',
            ],
        ];
    }
}

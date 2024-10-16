<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSSSContributionRequest extends FormRequest
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
                "min:0",
                'max:999999',
                'decimal:0,2',
                "numeric",
            ],
            'range_to' => [
                "required",
                "min:0",
                'max:999999',
                'decimal:0,2',
                "numeric",
            ],
            'employee_share' => [
                "required",
                "min:0",
                'max:999999',
                'decimal:0,2',
                "numeric",
            ],
            'employer_share' => [
                "required",
                "min:0",
                'max:999999',
                'decimal:0,2',
                "numeric",
            ],
            'employee_compensation' => [
                "required",
                "min:0",
                'max:999999',
                'decimal:0,2',
                "numeric",
            ],
            'employer_compensation' => [
                "required",
                "min:0",
                'max:999999',
                'decimal:0,2',
                "numeric",
            ],
            'employee_wisp' => [
                "required",
                "min:0",
                'max:999999',
                'decimal:0,2',
                "numeric",
            ],
            'employer_wisp' => [
                "required",
                "min:0",
                'max:999999',
                'decimal:0,2',
                "numeric",
            ],
        ];
    }
}

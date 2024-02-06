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
            'range_from'=> [
                "required",
                'decimal:8,2',
                "numeric",
            ],
            'range_to'=>[
                "required",
                'decimal:8,2',
                "numeric",
            ],
            'employee_share'=>[
                "required",
                'decimal:8,2',
                "numeric",
            ],
            'employer_share'=>[
                "required",
                'decimal:8,2',
                "numeric",
            ],
            'employee_contribution'=>[
                "required",
                'decimal:8,2',
                "numeric",
            ],
            'employer_contribution'=>[
                "required",
                'decimal:8,2',
                "numeric",
            ],
        ];
    }
}

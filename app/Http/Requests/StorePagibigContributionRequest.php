<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePagibigContributionRequest extends FormRequest
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
                "numeric",
                'max:999999',
                'decimal:0,2',
            ],
            'range_to'=>[
                "nullable",
                "numeric",
                'max:999999',
                'decimal:0,2',
            ],
            'employee_share_percent'=>[
                "required",
                "numeric",
                'max:999999',
                'decimal:0,2',
            ],
            'employer_share_percent'=>[
                "required",
                "numeric",
                'max:999999',
                'decimal:0,2',
            ],
            'employee_maximum_contribution'=>[
                "required",
                "numeric",
                'max:999999',
                'decimal:0,2',
            ],
            'employer_maximum_contribution'=>[
                "required",
                "numeric",
                'max:999999',
                'decimal:0,2',
            ],
            'employee_compensation'=>[
                "required",
                "numeric",
                'max:999999',
                'decimal:0,2',
            ],
            'employer_compensation'=>[
                "required",
                "numeric",
                'max:999999',
                'decimal:0,2',
            ],
        ];
    }
}

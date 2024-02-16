<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSSSContributionRequest extends FormRequest
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
                'digits_between:1,8',
                'decimal:0,2',
                "numeric",
            ],
            'range_to'=>[
                "nullable",
                'digits_between:1,8',
                'decimal:0,2',
                "numeric",
            ],
            'employee_share'=>[
                "nullable",
                'digits_between:1,8',
                'decimal:0,2',
                "numeric",
            ],
            'employer_share'=>[
                "nullable",
                'digits_between:1,8',
                'decimal:0,2',
                "numeric",
            ],
            'employee_contribution'=>[
                "nullable",
                'digits_between:1,8',
                'decimal:0,2',
                "numeric",
            ],
            'employer_contribution'=>[
                "nullable",
                'digits_between:1,8',
                'decimal:0,2',
                "numeric",
            ],
        ];
    }
}

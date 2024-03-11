<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePhilhealthContributionRequest extends FormRequest
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
                "min:0",
                'max:999999',
                'decimal:0,2',
            ],
            'range_to'=>[
                "nullable",
                "numeric",
                "min:0",
                'max:999999',
                'decimal:0,2',
            ],
            'employee_share'=>[
                "required",
                "numeric",
                "min:0",
                'max:999999',
                'decimal:0,2',
            ],
            'employer_share'=>[
                "required",
                "numeric",
                "min:0",
                'max:999999',
                'decimal:0,2',
            ],
            'share_type'=>[
                "required",
                "in:Amount,Percentage",
            ],
        ];
    }
}

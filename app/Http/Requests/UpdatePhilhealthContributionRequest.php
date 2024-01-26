<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePhilhealthContributionRequest extends FormRequest
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
            //
            'range_from'=> [
                "nullable",
                "numeric",
            ],
            'range_to'=>[
                "nullable",
                "numeric",
            ],
            'employee_share'=>[
                "nullable",
                "numeric",
            ],
            'employer_share'=>[
                "nullable",
                "numeric",
            ],
            'share_type'=>[
                "nullable",
                "in:Amount,Percentage",
            ],
        ];
    }
}

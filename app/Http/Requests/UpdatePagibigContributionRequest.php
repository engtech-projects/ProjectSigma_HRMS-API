<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePagibigContributionRequest extends FormRequest
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
            ],
            'range_to'=>[
                "nullable",
                "numeric",
            ],
            'employee_share_percent'=>[
                "nullable",
                "numeric",
            ],
            'employer_share_percent'=>[
                "nullable",
                "numeric",
            ],
            'employee_maximum_contribution'=>[
                "nullable",
                "numeric",
            ],
            'employer_maximum_contribution'=>[
                "nullable",
                "numeric",
            ],
            'employee_compensation'=>[
                "nullable",
                "numeric",
            ],
            'employer_compensation'=>[
                "nullable",
                "numeric",
            ],
        ];
    }
}

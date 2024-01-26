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
            ],
            'range_to'=>[
                "nullable",
                "numeric",
            ],
            'employee_share_percent'=>[
                "required",
                "numeric",
            ],
            'employer_share_percent'=>[
                "required",
                "numeric",
            ],
            'employee_maximum_contribution'=>[
                "required",
                "numeric",
            ],
            'employer_maximum_contribution'=>[
                "required",
                "numeric",
            ],
            'employee_compensation'=>[
                "required",
                "numeric",
            ],
            'employer_compensation'=>[
                "required",
                "numeric",
            ],
        ];
    }
}

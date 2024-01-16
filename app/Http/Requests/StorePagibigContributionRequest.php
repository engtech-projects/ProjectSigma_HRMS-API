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
                "required",
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
            'max_contribution'=>[
                "required",
                "numeric",
            ],
        ];
    }
}

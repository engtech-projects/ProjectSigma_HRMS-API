<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHMORequest extends FormRequest
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
            'hmo_name' => [
                "nullable",
                "string",
            ],
            'hmo_start' => [
                "nullable",
                "date",
            ],
            'hmo_end' => [
                "nullable",
                "date",
            ],
            'employee_share' => [
                "nullable",
                "numeric",
                "min:0",
                'max:999999',
                'decimal:0,2',
            ],
            'employer_share' => [
                "nullable",
                "numeric",
                "min:0",
                'max:999999',
                'decimal:0,2',
            ],
        ];
    }
}

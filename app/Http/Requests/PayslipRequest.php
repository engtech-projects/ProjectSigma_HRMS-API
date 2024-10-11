<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayslipRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if (gettype($this->ids) == "string") {
            $this->merge([
                'ids' => json_decode($this->ids, true),
            ]);
        }
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ids' => [
                'required',
                'array'
            ],
            'ids.*' => [
                'required',
                'integer',
                'exists:payroll_details,id'
            ],
            'hr' => [
                'nullable',
                'string',
            ],
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateDtrRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        $this->merge([
            'employee_ids' => json_decode($this->employee_ids, true),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cutoff_start' => 'required|date_format:Y-m-d',
            'cutoff_end' => 'required|date_format:Y-m-d',
            'employee_ids' => 'exists:employees,id',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSalaryGradeLevelRequest extends FormRequest
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
            'salary_grade_level' => 'required|string|unique:salary_grade_levels,salary_grade_level,' . $this->id . 'id',
            'salary_grade_step' => 'required|array',
            'salary_grade_step.*.step_name' => 'required|numeric',
        ];
    }
}

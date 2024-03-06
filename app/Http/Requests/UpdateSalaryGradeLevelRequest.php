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
        $id = $this->route('salary_grade_level')->id;
        return [
            'salary_grade_level' => 'required|string|unique:salary_grade_levels,salary_grade_level,' . $id . ',id',
            'salary_grade_step' => 'required|array',
            'salary_grade_step.*.id' => 'required|integer',
            'salary_grade_step.*.step_name' => 'required|numeric',
        ];
    }
}

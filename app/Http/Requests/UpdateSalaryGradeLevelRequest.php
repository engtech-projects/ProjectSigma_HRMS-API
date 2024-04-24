<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

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
        $id = $this->route('resource');
        return [
            'salary_grade_level' => [
                'required',
                'string',
                Rule::unique('salary_grade_levels')->where(function ($query) {
                    $query->whereNull('deleted_at');
                })->ignore($id)
            ],
            'salary_grade_step' => 'required|array',
            'salary_grade_step.*.id' => 'required|integer',
            'salary_grade_step.*.step_name' => 'required|numeric',
            'salary_grade_step.*.monthly_salary_amount' => 'required|numeric',
        ];
    }
}

<?php

namespace App\Http\Requests;

use App\Models\Department;
use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Request13thMonthDraft extends FormRequest
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
            "date_requested" => [
                "required",
                "date",
                "after_or_equal:today",
            ],
            "date_from" => [
                "required",
                "date",
            ],
            "date_to" => [
                "required",
                "date",
                "after_or_equal:date_from",
            ],
            "employee_ids" => [
                "required",
                "array",
            ],
            "employee_ids.*" => [
                "required",
                "integer",
                "exists:employees,id",
            ],
            "days_advance" => [
                "required",
                "integer",
                "min:0",
            ],
            "charging_type" => [
                Rule::when(
                    fn () => $this->days_advance === 0,
                    ['nullable'],
                    ['required']
                ),
                "string",
                "in:". Project::class.",".Department::class,
             ],
            "charging_id" => [
                "required_with:charging_type",
                "integer",
                Rule::when(
                    fn () => $this->charging_type === Project::class,
                    ['exists:projects,id'],
                    ['exists:departments,id']
                ),
            ]
        ];
    }
}

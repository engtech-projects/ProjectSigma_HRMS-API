<?php

namespace App\Http\Requests;

use App\Enums\AssignTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreEmployeeAllowancesRequest extends FormRequest
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
            'group_type' => [
                "required",
                "string",
                new Enum(AssignTypes::class)
            ],
            'employee_id' => [
                'required',
                "integer",
                "exists:employees,id",
            ],
            'project_id' => [
                'required_if:group_type,==,project',
                "integer",
                "exists:projects,id",
            ],
            'department_id' => [
                'required_if:group_type,==,department',
                "integer",
                "exists:departments,id",
            ],
            'allowance_date' => [
                "required",
                "date",
                'date_format:Y-m-d'
            ],
        ];
    }
}

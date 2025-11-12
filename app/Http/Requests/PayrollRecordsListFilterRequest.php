<?php

namespace App\Http\Requests;

use App\Enums\AssignTypes;
use App\Enums\PayrollType;
use App\Enums\ReleaseType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class PayrollRecordsListFilterRequest extends FormRequest
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
            "payroll_date" => [
                "nullable",
                "date",
                "date_format:Y-m-d"
            ],
            "payroll_type" => [
                "nullable",
                "string",
                new Enum(PayrollType::class),
            ],
            "release_type" => [
                "nullable",
                "string",
                new Enum(ReleaseType::class),
            ],
            'charging_type' => [
                'nullable',
                new Enum(AssignTypes::class)
            ],
            'project_id' => [
                'required_if:charging_type,==,' . AssignTypes::PROJECT->value,
                'nullable',
                "integer",
                "exists:projects,id",
            ],
            'department_id' => [
                'required_if:charging_type,==,' . AssignTypes::DEPARTMENT->value,
                'nullable',
                "integer",
                "exists:departments,id",
            ],
        ];
    }
}

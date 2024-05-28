<?php

namespace App\Http\Requests;

use App\Enums\AssignTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Http\Traits\HasApprovalValidation;

class UpdateEmployeeAllowancesRequest extends FormRequest
{
    use HasApprovalValidation;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if (gettype($this->employees) == "string") {
            $this->merge([
                "employees" => json_decode($this->employees, true),
            ]);
        }
        $this->prepareApprovalValidation();
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
                "nullable",
                "string",
                new Enum(AssignTypes::class)
            ],
            'employees' => [
                "nullable",
                "array",
            ],
            'employees.*' => [
                "nullable",
                "integer",
                "exists:employees,id",
            ],
            'project_id' => [
                'nullable_if:group_type,==,'.AssignTypes::PROJECT->value,
                "integer",
                "exists:projects,id",
            ],
            'department_id' => [
                'nullable_if:group_type,==,'.AssignTypes::DEPARTMENT->value,
                "integer",
                "exists:departments,id",
            ],
            'allowance_date' => [
                "nullable",
                "date",
                'date_format:Y-m-d'
            ],
            'cutoff_start' => [
                "nullable",
                "date",
                'date_format:Y-m-d'
            ],
            'cutoff_end' => [
                "nullable",
                "date",
                'date_format:Y-m-d'
            ],
            'total_days' => [
                "nullable",
                "integer",
            ],
            ...$this->updateApprovals(),
        ];
    }
}

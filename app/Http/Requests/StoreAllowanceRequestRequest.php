<?php

namespace App\Http\Requests;

use App\Enums\AssignTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Http\Traits\HasApprovalValidation;

class StoreAllowanceRequestRequest extends FormRequest
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
                "required",
                "string",
                new Enum(AssignTypes::class)
            ],
            'employees' => [
                "required",
                "array",
            ],
            'employees.*' => [
                "required",
                "integer",
                "exists:employees,id",
            ],
            'project_id' => [
                'required_if:group_type,==,' . AssignTypes::PROJECT->value,
                'nullable',
                "integer",
                "exists:projects,id",
            ],
            'department_id' => [
                'required_if:group_type,==,' . AssignTypes::DEPARTMENT->value,
                'nullable',
                "integer",
                "exists:departments,id",
            ],
            'allowance_date' => [
                "required",
                "date",
                'date_format:Y-m-d'
            ],
            'cutoff_start' => [
                "required",
                "date",
                'date_format:Y-m-d'
            ],
            'cutoff_end' => [
                "required",
                "date",
                'date_format:Y-m-d'
            ],
            'total_days' => [
                "required",
                "integer",
            ],
            ...$this->storeApprovals(),
        ];
    }
}

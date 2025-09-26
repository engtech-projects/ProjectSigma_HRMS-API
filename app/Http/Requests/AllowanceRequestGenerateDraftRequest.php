<?php

namespace App\Http\Requests;

use App\Enums\AssignTypes;
use App\Http\Traits\HasApprovalValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class AllowanceRequestGenerateDraftRequest extends FormRequest
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
            'employees' => [
                "required",
                "array",
            ],
            'employees.*' => [
                "required",
                "integer",
                "exists:employees,id",
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
                "numeric",
                "min:1",
                'decimal:0,2',
            ],
            'charging_type' => [
                "required",
                "string",
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
            ...$this->storeApprovals(),
        ];
    }
}

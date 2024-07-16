<?php

namespace App\Http\Requests;

use App\Enums\AssignTypes;
use App\Enums\RequestApprovalStatus;
use App\Enums\StringRequestApprovalStatus;
use App\Http\Traits\HasApprovalValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreOvertimeRequest extends FormRequest
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
        $this->prepareApprovalValidation();
        if (gettype($this->employees) == "string") {
            $this->merge([
                "employees" => json_decode($this->employees, true),
            ]);
        }
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
            "charging" => [
                "string",
                "required",
                "in:Department,Project"
            ],
            'project_id' => [
                "nullable",
                "required_if:charging,Project",
                "integer",
                "exists:projects,id",
            ],
            'department_id' => [
                "nullable",
                "required_if:charging,Department",
                "integer",
                "exists:departments,id",
            ],
            'overtime_date' => [
                "required",
                "date",
                "date_format:Y-m-d",
            ],
            'overtime_start_time' => [
                "required",
                'date_format:H:i',
            ],
            'overtime_end_time' => [
                "required",
                'date_format:H:i',
                'after:overtime_start_time',
            ],
            'reason' => [
                "required",
                "string",
            ],
            'meal_deduction' => [
                "required",
                "boolean",
            ],
            'request_status' => [
                "nullable",
                "string",
                new Enum(StringRequestApprovalStatus::class)
            ],
            ...$this->storeApprovals(),
        ];
    }
}

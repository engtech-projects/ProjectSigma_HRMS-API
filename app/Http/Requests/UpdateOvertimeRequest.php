<?php

namespace App\Http\Requests;

use App\Enums\RequestStatusType;
use App\Enums\StringRequestApprovalStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateOvertimeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if (gettype($this->approvals) == "string") {
            $this->merge([
                "approvals" => json_decode($this->approvals, true)
            ]);
        }
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
                "nullable",
                "array",
            ],
            'employees.*' => [
                "nullable",
                "integer",
                "exists:employees,id",
            ],
            'project_id' => [
                "nullable",
                "integer",
                "exists:projects,id"
            ],
            'department_id' => [
                "nullable",
                "integer",
                "exists:departments,id"
            ],
            'overtime_date' => [
                "nullable",
                "date",
                "date_format:Y-m-d",
            ],
            'overtime_start_time' => [
                "nullable",
                'date_format:H:i',
            ],
            'overtime_end_time' => [
                "nullable",
                'date_format:H:i',
                'after:overtime_start_time',
            ],
            'reason' => [
                "nullable",
                "string",
            ],
            'meal_deduction' => [
                "nullable",
                "boolean",
            ],
            'approvals' => [
                "nullable",
                "array",
            ],
            'approvals.*' => [
                "nullable",
                "array",
            ],
            'approvals.*.type' => [
                "nullable",
                "string",
            ],
            'approvals.*.user_id' => [
                "nullable",
                "integer",
                "exists:users,id",
            ],
            'approvals.*.status' => [
                "nullable",
                "string",
            ],
            'approvals.*.date_approved' => [
                "nullable",
                "date",
            ],
            'approvals.*.remarks' => [
                "nullable",
                "string",
            ],
            'request_status' => [
                "nullable",
                "string",
                new Enum(StringRequestApprovalStatus::class)
            ],
        ];
    }
}

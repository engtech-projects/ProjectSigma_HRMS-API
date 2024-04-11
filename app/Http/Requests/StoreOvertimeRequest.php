<?php

namespace App\Http\Requests;

use App\Enums\RequestApprovalStatus;
use App\Enums\StringRequestApprovalStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreOvertimeRequest extends FormRequest
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
                "approvals" => json_decode($this->approvals, true),
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
                "required",
                "array",
            ],
            'employees.*' => [
                "required",
                "integer",
                "exists:employees,id",
            ],
            'project_id' => [
                "required",
                "integer",
                "exists:projects,id"
            ],
            'department_id' => [
                "required",
                "integer",
                "exists:departments,id"
            ],
            'overtime_date' => [
                "required",
                "date",
                "date_format:Y-m-d",
            ],
            'overtime_start_time' => [
                "required",
                'date_format:H:i:s',
            ],
            'overtime_end_time' => [
                "required",
                'date_format:H:i:s',
                'after:overtime_start_time',
            ],
            'reason' => [
                "required",
                "string",
            ],
            'prepared_by' => [
                "required",
                "integer",
                "exists:users,id",
            ],
            'approvals' => [
                "required",
                "array",
            ],
            'approvals.*' => [
                "required",
                "array",
            ],
            'approvals.*.type' => [
                "required",
                "string",
            ],
            'approvals.*.user_id' => [
                "nullable",
                "integer",
                "exists:users,id",
            ],
            'approvals.*.status' => [
                "required",
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
                "required",
                "string",
                new Enum(StringRequestApprovalStatus::class)
            ],
        ];
    }
}

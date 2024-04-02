<?php

namespace App\Http\Requests;

use App\Enums\RequestApprovalStatus;
use App\Enums\RequestStatusType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateCashAdvanceRequest extends FormRequest
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
            'employee_id' => [
                "nullable",
                "integer",
                "exists:employees,id",
            ],
            'department_id' => [
                "nullable",
                "integer",
                "exists:departments,id",
            ],
            'project_id' => [
                "nullable",
                "integer",
                "exists:projects,id",
            ],
            'amount_requested' => [
                "nullable",
                "numeric",
                "min:1",
                'decimal:0,2',
            ],
            'amount_approved' => [
                "nullable",
                "numeric",
                "min:1",
                'decimal:0,2',
            ],
            'purpose' => [
                "nullable",
                "string",
            ],
            'terms_of_cash_advance' => [
                "nullable",
                "string",
            ],
            'remarks' => [
                "nullable",
                "string",
            ],
            'approvals' => [
                "nullable",
                "array",
            ],
            'approvals.*' => [
                "nullable",
                "array",
                new Enum(RequestApprovalStatus::class)
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
                new Enum(RequestStatusType::class)
            ],
            'released_by' => [
                "nullable",
                "integer",
                "exists:users,id",
            ],
        ];
    }
}

<?php

namespace App\Http\Requests;

use App\Enums\RequestStatusType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreCashAdvanceRequest extends FormRequest
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
        $this->merge([
            "approvals" => json_decode($this->approvals, true)
        ]);
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
                "required",
                "integer",
                "exists:employees,id",
            ],
            'department_id' => [
                "required",
                "integer",
                "exists:departments,id",
            ],
            'project_id' => [
                "required",
                "integer",
                "exists:projects,id",
            ],
            'amount_requested' => [
                "required",
                "numeric",
                "min:1",
                'decimal:0,2',
            ],
            'amount_approved' => [
                "required",
                "numeric",
                "min:1",
                'decimal:0,2',
            ],
            'purpose' => [
                "required",
                "string",
            ],
            'terms_of_cash_advance' => [
                "required",
                "string",
            ],
            'remarks' => [
                "required",
                "string",
            ],
            'approvals' => [
                "required",
                "array",
            ],
            'approvals.*' => [
                "required",
                "array",
                "required_array_keys:type,user_id,status,date_approved,remarks",
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
                new Enum(RequestStatusType::class)
            ],
            'released_by' => [
                "required",
                "integer",
                "exists:users,id",
            ],
        ];
    }
}

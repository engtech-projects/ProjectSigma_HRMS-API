<?php

namespace App\Http\Requests;

use App\Enums\LeaveRequestStatusType;
use App\Enums\LeaveRequestType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreEmployeeLeavesRequest extends FormRequest
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
                "nullable",
                "integer",
                "exists:departments,id",
            ],
            'project_id' => [
                "nullable",
                "integer",
                "exists:projects,id",
            ],
            'leave_id' => [
                "integer",
                "required",
                "exists:leaves,id",
            ],
            'other_absence' => [
                "nullable",
                "string",
                "exclude_if:type,Sick/Checkup,Special Celebration,Vacation,Mandatory Leave,Bereavement,Maternity/Paternity",
                'required_if:type,==,Other',
            ],
            'date_of_absence_from' => [
                "required",
                "date",
                "date_format:Y-m-d",
            ],
            'date_of_absence_to' => [
                "required",
                "date",
                "date_format:Y-m-d",
                "after_or_equal:date_of_absence_from"
            ],
            'reason_for_absence' => [
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
            'number_of_days' => [
                "required",
                "integer",
                "gt:0",
            ],
            'with_pay' => [
                "required",
                "boolean",
            ],
        ];
    }
}

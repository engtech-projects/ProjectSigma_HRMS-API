<?php

namespace App\Http\Requests;

use App\Enums\LeaveRequestStatusType;
use App\Enums\LeaveRequestType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateEmployeeLeavesRequest extends FormRequest
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
            'type' => [
                "nullable",
                "string",
                new Enum(LeaveRequestType::class)
            ],
            'other_absence' => [
                "nullable",
                "string",
                "exclude_if:type,Sick/Checkup,Special Celebration,Vacation,Mandatory Leave,Bereavement,Maternity/Paternity",
                'required_if:type,==,Other',
            ],
            'date_of_absence_from' => [
                "nullable",
                "date",
            ],
            'date_of_absence_to' => [
                "nullable",
                "date",
                "after:date_of_absence_from"
            ],
            'reason_for_absence' => [
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
                "required_array_keys:type,user_id,status,date_approved,remarks",
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
                new Enum(LeaveRequestStatusType::class)
            ],
        ];
    }
}

<?php

namespace App\Http\Requests;

use App\Enums\LeaveRequestStatusType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Http\Traits\HasApprovalValidation;

class UpdateEmployeeLeavesRequest extends FormRequest
{
    use HasApprovalValidation;
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
        $rules = [
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
                "nullable",
                "date",
                "date_format:Y-m-d",
            ],
            'date_of_absence_to' => [
                "nullable",
                "date",
                "date_format:Y-m-d",
                "after_or_equal:date_of_absence_from"
            ],
            'reason_for_absence' => [
                "nullable",
                "string",
            ],
            'request_status' => [
                "nullable",
                "string",
                new Enum(LeaveRequestStatusType::class)
            ],
            'number_of_days' => [
                "nullable",
                "numeric",
                "gt:0",
            ],
            'with_pay' => [
                "nullable",
                "boolean",
            ],
        ];
        return array_merge($rules, $this->storeApprovals());
    }
}

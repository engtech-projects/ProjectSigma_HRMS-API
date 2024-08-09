<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Traits\HasApprovalValidation;

class StoreEmployeeLeavesRequest extends FormRequest
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
                "required",
                "integer",
                "exists:employees,id",
            ],
            "charging" => [
                "string",
                "required",
                "in:Department,Project"
            ],
            'department_id' => [
                "nullable",
                "required_if:charging,Department",
                "integer",
                "exists:departments,id",
            ],
            'project_id' => [
                "nullable",
                "required_if:charging,Project",
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
            'number_of_days' => [
                "required",
                "numeric",
                "gt:0",
            ],
            'with_pay' => [
                "required",
                "boolean",
            ],
        ];
        return array_merge($rules, $this->storeApprovals());
    }
}

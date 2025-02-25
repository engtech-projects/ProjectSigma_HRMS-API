<?php

namespace App\Http\Requests;

use App\Enums\RequestStatusType;
use App\Http\Requests\Traits\PayrollLockValidationTrait;
use App\Models\Employee;
use App\Models\Leave;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Traits\HasApprovalValidation;
use App\Models\EmployeeLeaves;

class StoreEmployeeLeavesRequest extends FormRequest
{
    use HasApprovalValidation;
    use PayrollLockValidationTrait;
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
                function ($attribute, $value, $fail) {
                    if ($this->hasConflictedLeaveRequest($value)) {
                        $employeeName = $this->getEmployeeName($value);
                        $fail("LEAVE CONFLICT ERROR:" . $employeeName . 'already has a pending/approved leave request conflicting dates from '.$this->date_of_absence_from. ' to ' .$this->date_of_absence_to);
                    }
                },
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
                function ($attribute, $value, $fail) {
                    if ($this->isPayrollLocked($this->date_of_absence_from)) {
                        $fail("Payroll is locked for this Leave date.");
                    }
                },
            ],
            'date_of_absence_to' => [
                "required",
                "date",
                "date_format:Y-m-d",
                "after_or_equal:date_of_absence_from",
                function ($attribute, $value, $fail) {
                    if ($this->isPayrollLocked($this->date_of_absence_to)) {
                        $fail("Payroll is locked for this Leave date.");
                    }
                },
            ],
            'reason_for_absence' => [
                "required",
                "string",
            ],
            'number_of_days' => [
                "required",
                "numeric",
                "gt:0",
                function ($attribute, $value, $fail) {
                    if ($this->exceedsLeaveBalance() && $this->with_pay) {
                        $leaveRequestType = $this->getLeaveRequestType();
                        $fail("Not enough ".$leaveRequestType." balance");
                    }
                },
            ],
            'with_pay' => [
                "required",
                "boolean",
            ],
        ];
        return array_merge($rules, $this->storeApprovals());
    }
    protected function getEmployeeName($employeeId)
    {
        $employee = Employee::find($employeeId);
        return $employee ? $employee->fullname_first : 'Unknown';
    }
    protected function exceedsLeaveBalance()
    {
        $employee = Employee::find($this->employee_id);
        if (!$employee) {
            return true;
        }
        $leaveCredits = $employee->getLeaveCreditsAttribute();
        $leave = $leaveCredits->firstWhere('id', $this->leave_id);
        if (!$leave || $leave->balance < $this->number_of_days) {
            return true;
        }
        return false;
    }
    protected function getLeaveRequestType()
    {
        return Leave::where('id', $this->leave_id)->pluck('leave_name')->first();
    }
    protected function hasConflictedLeaveRequest($employeeId)
    {
        return EmployeeLeaves::where('employee_id')
        ->where(function ($query) {
            $query->where('date_of_absence_from', '<=', $this->date_of_absence_to)
                ->where('date_of_absence_to', '>=', $this->date_of_absence_from);
        })
        ->whereIn('request_status', [RequestStatusType::PENDING->value, RequestStatusType::APPROVED->value])
        ->exists();
    }
}

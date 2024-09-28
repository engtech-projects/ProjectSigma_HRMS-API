<?php

namespace App\Http\Requests;

use App\Enums\RequestStatusType;
use App\Enums\StringRequestApprovalStatus;
use App\Http\Traits\HasApprovalValidation;
use App\Models\Employee;
use App\Models\Overtime;
use Carbon\Carbon;
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
                function ($attribute, $value, $fail) {
                    if ($this->hasPendingOvertime($value)) {
                        $employeeName = $this->getEmployeeName($value);
                        $startTime12Hour = $this->formatTimeTo12Hour($this->overtime_start_time);
                        $endTime12Hour = $this->formatTimeTo12Hour($this->overtime_end_time);
                        $fail("OVERTIME CONFLICT ERROR:" . $employeeName . 'already has a pending/approved overtime request conflicting with ' . $this->overtime_date . ' '.$startTime12Hour. ' and ' .$endTime12Hour);
                    }
                },
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
    protected function hasPendingOvertime($employeeId)
    {
        return Overtime::whereHas('employees', function ($query) use ($employeeId) {
            $query->where('employee_id', $employeeId);
        })
        ->where(function ($query) {
            $query->where('overtime_date', $this->overtime_date)
                ->where(function ($query) {
                    $query->where('overtime_start_time', '<', $this->overtime_end_time)
                        ->where('overtime_end_time', '>', $this->overtime_start_time);
                });
        })
        ->whereIn('request_status', [RequestStatusType::PENDING->value, RequestStatusType::APPROVED->value])
        ->exists();
    }

    protected function getEmployeeName($employeeId)
    {
        $employee = Employee::find($employeeId);
        return $employee ? $employee->fullname_first : 'Unknown';
    }
    protected function formatTimeTo12Hour($time)
    {
        return Carbon::createFromFormat('H:i', $time)->format('g:i A');
    }
}

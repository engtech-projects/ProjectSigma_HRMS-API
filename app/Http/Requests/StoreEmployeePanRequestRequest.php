<?php

namespace App\Http\Requests;

use App\Enums\EmploymentStatus;
use App\Enums\HireSourceType;
use App\Enums\RequestStatuses;
use App\Enums\SalaryRequestType;
use App\Enums\WorkLocation;
use App\Http\Traits\HasApprovalValidation;
use App\Models\EmployeePanRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreEmployeePanRequestRequest extends FormRequest
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
        // GENERAL FIELDS
        // $this->merge(['date_of_effictivity' => null]);
        // $this->merge(['type' => null]);
        // $this->merge(['comments' => null]);
        // NEWHIRE EXCLUSIVE FIELDS
        // $this->merge(['pan_job_applicant_id' => null]);
        // $this->merge(['company_id_num' => null]);
        // $this->merge(['hire_source' => null]);
        // NEWHIRE/PROMOTION FIELDS
        // $this->merge(['employment_status' => null]);
        // $this->merge(['salary_grades' => null]);
        // NEWHIRE/TRANSFER FIELDS
        // $this->merge(['work_location' => null]);
        // $this->merge(['section_department_id' => null]);
        // $this->merge(['projects' => null]);
        // NEWHIRE/TRANSFER/PROMOTION FIELDS
        // $this->merge(['salary_type' => null]);
        // $this->merge(['designation_position' => null]);
        // TRANSFER/PROMOTION/TERMINATION FIELDS
        // $this->merge(['employee_id' => null]);
        // TERMINATION EXCLUSIVE FIELDS
        // $this->merge(['type_of_termination' => null]);
        // $this->merge(['reasons_for_termination' => null]);
        // $this->merge(['eligible_for_rehire' => null]);
        // $this->merge(['last_day_worked' => null]);
        $this->prepareApprovalValidation();
        if ($this->type === 'New Hire') {
            // REMOVE TERMINATION EXCLUSIVE FIELDS
            $this->merge(['type_of_termination' => null]);
            $this->merge(['reasons_for_termination' => null]);
            $this->merge(['eligible_for_rehire' => null]);
            $this->merge(['last_day_worked' => null]);
            // REMOVE TRANSFER/PROMOTION/TERMINATION FIELDS
            $this->merge(['employee_id' => null]);
        }
        if ($this->type === 'Transfer') {
            // REMOVE NEWHIRE EXCLUSIVE FIELDS
            $this->merge(['pan_job_applicant_id' => null]);
            $this->merge(['company_id_num' => null]);
            $this->merge(['hire_source' => null]);
            // REMOVE NEWHIRE/PROMOTION FIELDS
            $this->merge(['employment_status' => null]);
            $this->merge(['salary_grades' => null]);
            // REMOVE TERMINATION EXCLUSIVE FIELDS
            $this->merge(['type_of_termination' => null]);
            $this->merge(['reasons_for_termination' => null]);
            $this->merge(['eligible_for_rehire' => null]);
            $this->merge(['last_day_worked' => null]);
        }
        if ($this->type === 'Promotion') {
            // REMOVE NEWHIRE EXCLUSIVE FIELDS
            $this->merge(['pan_job_applicant_id' => null]);
            $this->merge(['company_id_num' => null]);
            $this->merge(['hire_source' => null]);
            // REMOVE NEWHIRE/TRANSFER FIELDS
            $this->merge(['work_location' => null]);
            $this->merge(['section_department_id' => null]);
            $this->merge(['projects' => null]);
            // REMOVE TERMINATION EXCLUSIVE FIELDS
            $this->merge(['type_of_termination' => null]);
            $this->merge(['reasons_for_termination' => null]);
            $this->merge(['eligible_for_rehire' => null]);
            $this->merge(['last_day_worked' => null]);
        }
        if ($this->type === 'Termination') {
            // REMOVE NEWHIRE EXCLUSIVE FIELDS
            $this->merge(['pan_job_applicant_id' => null]);
            $this->merge(['company_id_num' => null]);
            $this->merge(['hire_source' => null]);
            // REMOVE NEWHIRE/PROMOTION FIELDS
            $this->merge(['employment_status' => null]);
            $this->merge(['salary_grades' => null]);
            // REMOVE NEWHIRE/TRANSFER FIELDS
            $this->merge(['work_location' => null]);
            $this->merge(['section_department_id' => null]);
            $this->merge(['projects' => null]);
            // REMOVE NEWHIRE/TRANSFER/PROMOTION FIELDS
            $this->merge(['salary_type' => null]);
            $this->merge(['designation_position' => null]);
        }
        // if ($this->type === 'Rehire') {
        //     $this->merge(['projects' => null]);
        // }
        // if ($this->type === 'Memo') {
        //     $this->merge(['projects' => null]);
        // }
        if ($this->work_location === WorkLocation::OFFICE->value && $this->projects === []) {
            $this->merge(['projects' => null]);
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
            'date_of_effictivity' => [
                "required",
                "date",
            ],
            'type' => [
                "required",
                "string",
                'in:New Hire,Termination,Transfer,Promotion'
            ],
            'pan_job_applicant_id' => [
                "nullable",
                "integer",
                "exists:job_applicants,id",
                'required_if:type,==,New Hire',
                function ($attribute, $value, $fail) {
                    if ($this->pendingNewHire($value)) {
                        $fail("This NEW HIRE has a pending PAN request");
                    }
                },
            ],
            'employee_id' => [
                "nullable",
                "integer",
                "exists:employees,id",
                'required_if:type,==,Transfer,Promotion,Termination',
                function ($attribute, $value, $fail) {
                    if ($this->pendingEmployee($value)) {
                        $fail("This EMPLOYEE has a pending PAN request");
                    }
                },
            ],
            'company_id_num' => [
                "nullable",
                "string",
                'required_if:type,==,New Hire',
            ],
            'hire_source' => [
                "nullable",
                "string",
                new Enum(HireSourceType::class),
                'required_if:type,==,New Hire',
            ],
            'employment_status' => [
                "nullable",
                "string",
                'required_if:type,==,New Hire,Promotion',
                new Enum(EmploymentStatus::class)
            ],
            'salary_type' => [
                "nullable",
                "string",
                'required_if:type,==,New Hire,Transfer,Promotion',
                new Enum(SalaryRequestType::class)
            ],
            'designation_position' => [
                "nullable",
                "integer",
                "exists:positions,id",
                'required_if:type,==,New Hire,Transfer,Promotion',
            ],
            'salary_grades' => [
                "nullable",
                "integer",
                "exists:salary_grade_steps,id",
                'required_if:type,==,New Hire,Promotion',
            ],
            'work_location' => [
                "nullable",
                "string",
                'required_if:type,==,New Hire,Transfer',
                "max:250",
            ],
            'section_department_id' => [
                "nullable",
                "integer",
                "exists:departments,id",
                'required_if:work_location,==,'.WorkLocation::OFFICE->value,
            ],
            'projects' => [
                "nullable",
                "array",
                "min:1",
                'required_if:work_location,==,'.WorkLocation::PROJECT->value,
            ],
            'projects.*' => [
                "nullable",
                "integer",
                "exists:projects,id",
                'required_if:work_location,==,'.WorkLocation::PROJECT->value,
            ],
            'type_of_termination' => [
                "nullable",
                "string",
                'required_if:type,==,Termination',
                "max:250",
            ],
            'reasons_for_termination' => [
                "nullable",
                "string",
                'required_if:type,==,Termination',
                "max:250",
            ],
            'eligible_for_rehire' => [
                "nullable",
                "string",
                'required_if:type,==,Termination',
                "max:250",
            ],
            'last_day_worked' => [
                "nullable",
                "string",
                'required_if:type,==,Termination',
                "max:250",
            ],
            'comments' => [
                "nullable",
                "string",
                "max:250",
            ],
            ...$this->storeApprovals(),
        ];
    }
    protected function pendingEmployee($employeeId)
    {
        return EmployeePanRequest::where('employee_id', $employeeId)
        ->whereIn('request_status', [RequestStatuses::PENDING->value,])
        ->exists();
    }
    protected function pendingNewHire($employeeId)
    {
        return EmployeePanRequest::where('pan_job_applicant_id', $employeeId)
        ->whereIn('request_status', [RequestStatuses::PENDING->value,])
        ->exists();
    }
}

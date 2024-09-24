<?php

namespace App\Http\Requests;

use App\Enums\EmploymentStatus;
use App\Enums\HireSourceType;
use App\Enums\SalaryRequestType;
use App\Http\Traits\HasApprovalValidation;
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
        $this->prepareApprovalValidation();
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
            ],
            'employee_id' => [
                "nullable",
                "integer",
                "exists:employees,id",
                'required_if:type,==,Termination,Transfer,Promotion',
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
                'required_if:type,==,New Hire',
                new Enum(EmploymentStatus::class)
            ],
            'salary_type' => [
                "nullable",
                "string",
                new Enum(SalaryRequestType::class)
            ],
            'designation_position' => [
                "nullable",
                "integer",
                "exists:positions,id",
                'required_if:type,==,New Hire',
            ],
            'salary_grades' => [
                "nullable",
                "integer",
                "exists:salary_grade_steps,id",
                'required_if:type,==,New Hire',
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
                'required_if:type,==,New Hire',
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
}

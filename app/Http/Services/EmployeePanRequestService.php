<?php

namespace App\Http\Services;

use App\Models\Employee;
use App\Exceptions\TransactionFailedException;
use App\Enums\EmployeeInternalWorkExperiencesStatus;
use App\Models\EmployeePersonnelActionNoticeRequest;
use Exception;

class EmployeePanRequestService
{
    protected $panRequest;
    public function __construct(EmployeePersonnelActionNoticeRequest $panRequest)
    {
        $this->panRequest = $panRequest;
    }

    public function getAll()
    {
        return EmployeePersonnelActionNoticeRequest::with(['employee', 'jobapplicantonly', 'department'])->get();
    }

    public function create($attributes)
    {
        EmployeePersonnelActionNoticeRequest::create($attributes);
    }

    public function createInternalWorkExperiences(Employee $employee, EmployeePersonnelActionNoticeRequest $panRequest)
    {
        try {
            $employee->employee_internal()->create([
                "position_title" => $panRequest->designation_position,
                "employment_status" => "status",
                "department" => $panRequest->section_department_id,
                "immediate_supervisor" => $panRequest->immediate_supervisor ?? "N/A",
                "actual_salary" => $panRequest->salarygrade->monthly_salary_amount,
                "work_location" => $panRequest->work_location,
                "hire_source" => $panRequest->hire_source,
                "status" => EmployeeInternalWorkExperiencesStatus::CURRENT,
                "date_from" => $panRequest->date_from,
                "salary_grades" => $panRequest->salary_grades,
            ]);
        } catch (\Exception $e) {
            throw new Exception("Failed to create employee internal experience");
        }
    }
    public function toHireEmployee(EmployeePersonnelActionNoticeRequest $panRequest)
    {
        $jobApplicant = $panRequest->jobapplicantonly;
        $jobApplicant["first_name"] = $jobApplicant->firstname;
        $jobApplicant["family_name"] = $jobApplicant->lastname;
        $employee = Employee::create($jobApplicant->toArray());
        $this->createInternalWorkExperiences($employee, $panRequest);
    }

    public function toTransferEmployee(EmployeePersonnelActionNoticeRequest $panRequest)
    {
    }

    public function toPromoteEmployee(EmployeePersonnelActionNoticeRequest $panRequest)
    {
    }

    public function toTerminateEmployee(EmployeePersonnelActionNoticeRequest $panRequest)
    {
    }
}

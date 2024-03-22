<?php

namespace App\Http\Services;

use Exception;
use App\Models\Employee;
use App\Models\InternalWorkExperience;
use App\Exceptions\TransactionFailedException;
use App\Enums\EmployeeInternalWorkExperiencesStatus;
use App\Models\EmployeePersonnelActionNoticeRequest;

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
        $interWorkExp = InternalWorkExperience::where([
            "employee_id" => $panRequest->employee_id,
            "status" => EmployeeInternalWorkExperiencesStatus::CURRENT,
            "date_to" => null
        ])->firstOrFail();
        $interWorkExp->status = EmployeeInternalWorkExperiencesStatus::PREVIOUS;
        $interWorkExp->fill($panRequest->toArray());
        $interWorkExp->save();
    }

    public function toPromoteEmployee(EmployeePersonnelActionNoticeRequest $panRequest)
    {
        $interWorkExp = InternalWorkExperience::where(["employee_id" => $panRequest->employee_id])->firstOrfail();
        $panRequest->hire_source = $interWorkExp->hire_source;
        $panRequest->salary_grades = $interWorkExp->salary_grades;
        $interWorkExp->fill($panRequest->toArray());
        $interWorkExp->save();
    }

    public function toTerminateEmployee(EmployeePersonnelActionNoticeRequest $panRequest)
    {
        $interWorkExp = InternalWorkExperience::where(["employee_id" => $panRequest->employee_id])->firstOrfail();
        $panRequest->work_location = $interWorkExp->work_location;
        $panRequest->hire_source = $interWorkExp->hire_source;
        $panRequest->salary_grades = $interWorkExp->salary_grades;
        $interWorkExp->fill($panRequest->toArray());
        $interWorkExp->save();
    }

}

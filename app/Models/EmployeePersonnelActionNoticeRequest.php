<?php

namespace App\Models;

use Exception;
use App\Traits\HasUser;
use App\Traits\HasApproval;
use App\Enums\PersonelAccessForm;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use App\Enums\ManpowerRequestStatus;
use Illuminate\Database\Eloquent\Model;
use App\Enums\EmployeeRelatedPersonType;
use App\Enums\JobApplicationStatusEnums;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\EmployeeCompanyEmploymentsStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Enums\EmployeeInternalWorkExperiencesStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeePersonnelActionNoticeRequest extends Model
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use HasApproval;
    use HasUser;

    public const NEW_HIRE = "New Hire";
    public const TRANSFER = "Transfer";
    public const PROMOTION = "Promotion";
    public const TERMINATION = "Termination";


    protected $appends = [
        "fullname",
        "request_created_at"
    ];

    protected $casts = [
        "approvals" => "array",
        "created_at" => "date:Y-m-d",
        "date_of_effictivity" => "date:Y-m-d"
    ];

    protected $fillable = [
        'id',
        'employee_id',
        'type',
        'date_of_effictivity',
        'section_department_id',
        'designation_position',
        'hire_source',
        'work_location',
        'new_section_id',
        'new_location',
        'new_employment_status',
        'new_position',
        'type_of_termination',
        'reasons_for_termination',
        'eligible_for_rehire',
        'last_day_worked',
        'approvals',
        'created_by',
        'new_salary_grades',
        'pan_job_applicant_id',
        'salary_grades',
    ];


    /**
     * MODEL STATIC
     * METHODS
     *
     */
    public static function boot()
    {
        parent::boot();
        static::creating(function ($employeePersonnelActionNoticeRequest) {
            $employeePersonnelActionNoticeRequest->created_by = auth()->user()->id;
        });
    }

    public function getFullNameAttribute()
    {
        if ($this->type == "New Hire") {
            return $this->jobapplicant->lastname . ", " . $this->jobapplicant->firstname . " " . $this->jobapplicant->middlename;
        } else {
            return $this->employee->family_name . ", " . $this->employee->first_name . " " . $this->employee->middle_name;
        }
    }
    public function requestCreatedAt(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->created_at->format('F j, Y')
        );
    }


    public function scopeRequestStatusPending(Builder $query): void
    {
        $query->where('request_status', PersonelAccessForm::REQUESTSTATUS_PENDING);
    }
    public function scopeApproval($query)
    {
        return $query->where("request_status", "=", "Pending");
    }
    public function scopeCreatedBy(Builder $query, $id): Builder
    {
        return $query->where("created_by", $id);
    }

    public function jobapplicant(): HasOne
    {
        return $this->hasOne(JobApplicants::class, "id", "pan_job_applicant_id")->with('manpower');
    }

    public function jobapplicantname(): HasOne
    {
        return $this->hasOne(JobApplicants::class, "id", "user_id");
    }

    public function jobapplicantonly(): HasOne
    {
        return $this->hasOne(JobApplicants::class, "id", "pan_job_applicant_id");
    }

    public function salarygrade(): HasOne
    {
        return $this->hasOne(SalaryGradeStep::class, "id", "salary_grades");
    }

    public function manpower(): HasOne
    {
        return $this->hasOne(ManpowerRequest::class, "id", "job_applicants.manpowerrequests_id");
    }

    public function department(): HasOne
    {
        return $this->hasOne(Department::class, "id", "section_department_id");
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, "id", "employee_id");
    }

    public function completeRequestStatus()
    {
        DB::transaction(function () {
            switch ($this->type) {
                case EmployeePersonnelActionNoticeRequest::NEW_HIRE:
                    $this->hireRequest();
                    break;
                case EmployeePersonnelActionNoticeRequest::TRANSFER:
                    $this->transferRequest();
                    break;
                case EmployeePersonnelActionNoticeRequest::PROMOTION:
                    $this->promotionRequest();
                    break;
                case EmployeePersonnelActionNoticeRequest::TERMINATION:
                    $this->terminationRequest();
                    break;
            }
            $this->request_status = PersonelAccessForm::REQUESTSTATUS_APPROVED;
            $this->save();
            $this->refresh();
        });
    }
    public function denyRequestStatus()
    {

        $this->request_status = PersonelAccessForm::REQUESTSTATUS_DISAPPROVED;
        $this->save();
        $this->refresh();
    }

    public function requestStatusCompleted(): bool
    {
        if ($this->request_status == PersonelAccessForm::REQUESTSTATUS_APPROVED) {
            return true;
        }
        return false;
    }

    public function requestStatusEnded(): bool
    {
        if (
            in_array(
                $this->request_status,
                [
                    PersonelAccessForm::REQUESTSTATUS_DISAPPROVED,
                    PersonelAccessForm::REQUESTSTATUS_FILLED,
                    PersonelAccessForm::REQUESTSTATUS_HOLD,
                    PersonelAccessForm::REQUESTSTATUS_CANCELLED,
                ]
            )
        ) {
            return true;
        }
        return false;
    }

    /** Get InternalWorkExperience model
     * Parameters:
     * integer $employeeId from attribute in Employee PAN request model,
     * array $filters from attribute in Employee PAN request model
     */
    public function getInternalWorkExp(int $employeeId, ?array $filter = [])
    {
        return InternalWorkExperience::byEmployee($employeeId)->where($filter)->firstOrFail();
    }

    /** New Hire Employee PAN request approved
     * to hire proccessed
     * Get job applicant model in related in PAN request
     * create new Employee model from Job Applicant attributes
     * create InternalWorkExperience model from Employee model.
     */
    public function hireRequest()
    {
        // duplicate jobApplicant data to employee
        $jobApplicant = $this->jobapplicantonly;
        $jobApplicant["first_name"] = $jobApplicant->firstname;
        $jobApplicant["family_name"] = $jobApplicant->lastname;
        $employee = Employee::create($jobApplicant->toArray());

        // pan request details to internal work experience
        $employeeInternal = $this->toArray();
        unset($employeeInternal["id"]);
        $employeeInternal["status"] = EmployeeInternalWorkExperiencesStatus::CURRENT;
        $employeeInternal['actual_salary'] = $this->salarygrade;
        $employeeInternal["position_title"] = $this->designation_position;
        $employeeInternal["employment_status"] = $this->employement_status;
        $employeeInternal['immediate_supervisor'] = $jobApplicant->immediate_supervisor ?? "N/A";
        $employee->employee_internal()->create($employeeInternal);

        // employee related person details
        $employeeRelatedPerson = $this->employeeRelatedPersonDetails($employee);
        $employee->employee_related_person()->create($employeeRelatedPerson);

        if (property_exists("workexperience", $this)) {
            $employee->employee_externalwork()->createMany([
                "date_from" => $this->workexperience->inclusive_dates_from ?? null,
                "date_to" => $this->workexperience->inclusive_dates_to ?? null,
                "position_title" => $this->workexperience->position_title ?? null,
                "company_name" => $this->workexperience->dpt_agency_office_company ?? null,
                "salary" => $this->workexperience->monthly_salary ?? null,
                "status_of_appointment" => $this->workexperience->status_of_appointment ?? null,
            ]);
        }
        if (property_exists("children", $this)) {
            $employee->employee_related_person()->createMany([
                "name" => $this->children->name,
                "date_of_birth" => $this->children->birthdate ?? null,
                "type" => EmployeeRelatedPersonType::CHILD,
            ]);
        }

        // update status for job appicants and manpower
        $this->jobapplicantonly()->update(["status" => JobApplicationStatusEnums::HIRED]);
        $this->jobapplicantonly->manpower()->update(["request_status" => ManpowerRequestStatus::FILLED]);
    }

    private function employeeRelatedPersonDetails(): array
    {
        $jobApplicant = $this->jobapplicantonly;
        if ($jobApplicant->name_of_spouse) {
            $spouse = array(
                "name" => $jobApplicant->name_of_spouse,
                "date_of_birth" => $jobApplicant->date_of_birth_spouse ?? null,
                "contact_no" => $jobApplicant->telephone_spouse ?? null,
                "occupation" => $jobApplicant->occupation_spouse ?? null,
                "type" => EmployeeRelatedPersonType::SPOUSE,
            );
        }

        if ($jobApplicant->father_name) {
            $father = array(
                "name" => $jobApplicant->father_name,
                "type" => EmployeeRelatedPersonType::FATHER,
            );
        }

        if ($jobApplicant->mother_name) {
            $mother = array(
                "name" => $jobApplicant->mother_name,
                "type" => EmployeeRelatedPersonType::MOTHER,
            );
        }

        if ($jobApplicant->icoe_name) {
            $icoe_name = array(
                "name" => $jobApplicant->icoe_name ?? null,
                "street" => $jobApplicant->icoe_street ?? null,
                "brgy" => $jobApplicant->icoe_brgy ?? null,
                "city" => $jobApplicant->icoe_city ?? null,
                "zip" => $jobApplicant->icoe_zip ?? null,
                "province" => $jobApplicant->icoe_province ?? null,
                "relationship" => $jobApplicant->icoe_relationship ?? null,
                "contact_no" => $jobApplicant->telephone_icoe ?? null,
                "type" => EmployeeRelatedPersonType::CONTACT_PERSON
            );
        }
        return array_merge($spouse, $father, $mother, $icoe_name);
    }

    /** Transfer Employee PAN  request approved
     * to transfer proccessed.
     * Get InternalworkExperience related in PAN Request with status is current
     * Update Interenal Work Experience status to Previous
     * Fill InternalWorkExperience model attributes from PAN Request and save.
     */
    public function transferRequest()
    {
        $interWorkExp = $this->getInternalWorkExp($this->employee_id, [
            "status" => EmployeeInternalWorkExperiencesStatus::CURRENT,
            "date_to" => null
        ]);
        $interWorkExp->status = EmployeeInternalWorkExperiencesStatus::PREVIOUS;
        $interWorkExp->save();

        InternalWorkExperience::create([
            'department' => $this->new_section,
            'immediate_supervisor' => $this->immediate_supervisor ?? "N/A",
            'work_location' => $this->new_location,
            'date_from' => $this->date_of_effictivity,
            'employee_id' => $this->employee_id,
            'position_title' => $this->position_title,
            'employment_status' => $this->employment_status,
            'actual_salary' => $this->actual_salary,
            'hire_source' => $this->hire_source,
            'salary_grades' => $this->salary_grades,
            'status' => EmployeeInternalWorkExperiencesStatus::CURRENT,
            'date_to' => null,
        ]);
    }

    /** Promotion Employee PAN request approved
     * to promote employee proccessed.
     * Get InternalworkExperience related in PAN Request with status is current.
     * Save InternalWorkExperience model from attributes from InternalWorkExperience model.
     */
    public function promotionRequest()
    {
        $interWorkExp = $this->getInternalWorkExp($this->employee_id, [
            "date_to" => null,
            "status" => EmployeeInternalWorkExperiencesStatus::CURRENT
        ]);
        $interWorkExp->status = EmployeeInternalWorkExperiencesStatus::PREVIOUS;
        $interWorkExp->save();

        InternalWorkExperience::create([
            'employee_id' => $interWorkExp->employee_id,
            'position_title' => $this->designation_position,
            'employment_status' => $this->new_employment_status,
            'department' => $interWorkExp->department,
            'immediate_supervisor' => $interWorkExp->immediate_supervisor ?? "N/A",
            'actual_salary' => $this->salarygrade->monthly_salary_amount,
            'salary_grades' => $this->salary_grades,
            'date_from' => $this->date_from,
            'work_location' => $interWorkExp->work_location,
            'hire_source' => $interWorkExp->hire_source,
            'status' => EmployeeInternalWorkExperiencesStatus::CURRENT,
            'date_to' => null,
        ]);
    }
    /** Termination Employee PAN  request approved
     * to terminate proccessed.
     * Get InternalworkExperience related in PAN Request with the specific employee_id
     * Fill InternalWorkExperience model attributes from PAN Request and save.
     */
    public function terminationRequest()
    {

        $interWorkExp = $this->getInternalWorkExp($this->employee_id);
        $interWorkExp->date_to = date('Y-m-d');
        $interWorkExp->save();

        Termination::create([
            'employee_id' => $interWorkExp->id,
            'type_of_termination' => $this->type_of_termination,
            'reason_for_termination' => $this->reasons_for_termination,
            'eligible_for_rehire' => $this->eligible_for_rehire,
        ]);
    }
}

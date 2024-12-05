<?php

namespace App\Models;

use App\Enums\EmployeeAddressType;
use App\Traits\HasUser;
use App\Traits\HasApproval;
use App\Enums\PersonelAccessForm;
use Laravel\Sanctum\HasApiTokens;
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
use App\Enums\WorkLocation;
use App\Http\Traits\UploadFileTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Traits\ModelHelpers;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EmployeePanRequest extends Model
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use HasApproval;
    use HasUser;
    use UploadFileTrait;
    use ModelHelpers;

    public const NEW_HIRE = "New Hire";
    public const TRANSFER = "Transfer";
    public const PROMOTION = "Promotion";
    public const TERMINATION = "Termination";

    public const EMPLOYEE_WORK_ASSIGNMENT = 'App\Model\EmployeeWorkAssignment';
    protected $appends = [
        "fullname",
        "request_created_at"
    ];

    protected $table = "employee_pan_requests";

    protected $casts = [
        "approvals" => "array",
        "created_at" => "date:Y-m-d",
        "date_of_effictivity" => "date:Y-m-d",
        "children" => "array",
        "workexperience" => "array",
        "education" => "array",
    ];

    protected $fillable = [
        'id',
        'date_of_effictivity',
        'type',
        'pan_job_applicant_id',
        'employee_id',
        'company_id_num',
        'hire_source',
        'employment_status',
        'salary_type',
        'designation_position',
        'salary_grades',
        'work_location',
        'section_department_id',
        'type_of_termination',
        'reasons_for_termination',
        'eligible_for_rehire',
        'last_day_worked',
        'comments',
        'created_by',
        'approvals',
    ];

    protected $perPage = 10;

    /**
     * MODEL STATIC
     * METHODS
     *
     */
    public static function boot()
    {
        parent::boot();
        static::creating(function ($EmployeePanRequest) {
            $EmployeePanRequest->created_by = auth()->user()->id;
        });
    }

    /**
     * ==================================================
     * MODEL RELATIONSHIPS
     * ==================================================
     */
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

    public function position(): HasOne
    {
        return $this->hasOne(Position::class, "id", "designation_position");
    }
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, EmployeePanRequestProjects::class)
        ->withtimestamps();
    }
    /**
     * ==================================================
     * MODEL ATTRIBUTES
     * ==================================================
     */
     public function getFullNameAttribute()
    {
        if ($this->type == "New Hire") {
            return $this->jobapplicant?->fullname_last;
        } else {
            return $this->employee?->fullname_last;
        }
    }
    public function requestCreatedAt(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->created_at->format('F j, Y')
        );
    }
    public function getProjectNamesAttribute()
    {
        return $this->projects()->pluck('project_code');
    }
    public function getProjectIdsAttribute()
    {
        return $this->projects()->pluck('id');
    }
    public function getDepartmentNameAttribute()
    {
        return $this->department?->department_name;
    }

    /**
     * ==================================================
     * STATIC SCOPES
     * ==================================================
     */
     public function scopeRequestStatusPending(Builder $query): void
     {
         $query->where('request_status', PersonelAccessForm::REQUESTSTATUS_PENDING);
     }
     public function scopeApproval($query)
     {
         return $query->where("request_status", "=", "Pending");
     }
     public function scopeCreatedBy(Builder $query, $id): void
     {
         $query->where("created_by", $id);
     }
    /**
     * ==================================================
     * DYNAMIC SCOPES
     * ==================================================
     */
    /**
     * ==================================================
     * MODEL FUNCTIONS
     * ==================================================
     */
    public function completeRequestStatus()
    {
        switch ($this->type) {
            case EmployeePanRequest::NEW_HIRE:
                $this->hireRequest();
                break;
            case EmployeePanRequest::TRANSFER:
                $this->transferRequest();
                break;
            case EmployeePanRequest::PROMOTION:
                $this->promotionRequest();
                break;
            case EmployeePanRequest::TERMINATION:
                $this->terminationRequest();
                break;
        }
        $this->request_status = PersonelAccessForm::REQUESTSTATUS_APPROVED;
        $this->save();
        $this->refresh();
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
        $jobApplicant["middle_name"] = $jobApplicant->middlename;
        $jobApplicant["family_name"] = $jobApplicant->lastname;
        $jobApplicant["nick_name"] = $jobApplicant->nickname;
        $jobApplicant["name_suffix"] = $jobApplicant->name_suffix;
        $jobApplicant["nick_name"] = $jobApplicant->contact_info;
        $jobApplicant["blood_type"] = $jobApplicant->blood_type;
        $jobApplicant["gender"] = $jobApplicant->gender;
        $jobApplicant["civil_status"] = $jobApplicant->civil_status;
        $jobApplicant["date_of_marriage"] = ($jobApplicant["date_of_marriage"] === 'null') ? null : $jobApplicant["date_of_marriage"];
        $employee = Employee::create($jobApplicant->toArray());
        // pan request details to internal work experience
        $employeeInternal = $this->toArray();
        unset($employeeInternal["id"]);
        $employeeInternal["status"] = EmployeeInternalWorkExperiencesStatus::CURRENT;
        $employeeInternal['actual_salary'] = $this->salarygrade->monthly_salary_amount;
        $employeeInternal["position_id"] = $this->designation_position;
        $employeeInternal["employment_status"] = $this->employment_status;
        $employeeInternal['immediate_supervisor'] = $jobApplicant->immediate_supervisor ?? "N/A";
        $employeeInternal['department_id'] = $this->section_department_id;
        $employeeInternal['date_from'] = $this->date_of_effictivity;
        $employeeInternalWork = $employee->employee_internal()->create($employeeInternal);
        if ($this->work_location === WorkLocation::PROJECT->value) {
            $employeeInternalWork->projects()->attach($this->project_ids);
        }
        $employee->fileuploads()->create([
            "employee_uploads" => "Application Letter",
            "upload_type" => "Documents",
            "file_location" => $jobApplicant->application_letter_attachment,
        ]);
        $employee->fileuploads()->create([
            "employee_uploads" => "Resume",
            "upload_type" => "Documents",
            "file_location" => $jobApplicant->resume_attachment,
        ]);
        try {
            $pdf = Pdf::loadView('reports.docs.application_form', ['application' => $jobApplicant]);
            $filePath = EmployeeUploads::DOCS_DIR . 'pdfs/application_form.pdf';
            Storage::disk('public')->put(EmployeeUploads::DOCS_DIR . 'pdfs/application_form.pdf', $pdf->output());

            $employee->fileuploads()->create([
                "employee_uploads" => "Application Form",
                "upload_type" => "Documents",
                "file_location" => $filePath,
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
        //company employements
        $employee->company_employments()->create([
            "employeedisplay_id" => $this->company_id_num,
            "date_hired" => $this->date_of_effictivity,
            "phic_number" => $jobApplicant->philhealth ?: "N/A",
            "sss_number" => $jobApplicant->sss ?: "N/A",
            "tin_number" => $jobApplicant->tin ?: "N/A",
            "pagibig_number" => $jobApplicant->pagibig ?: "N/A",
            "status" => EmployeeCompanyEmploymentsStatus::ACTIVE,
        ]);
        // employee present address
        $employee->present_address()->create([
            "street" => $jobApplicant->pre_address_street ?: "N/A",
            "brgy" => $jobApplicant->pre_address_brgy ?: "N/A",
            "city" => $jobApplicant->pre_address_city ?: "N/A",
            "zip" => $jobApplicant->pre_address_zip ?: "N/A",
            "province" => $jobApplicant->pre_address_province ?: "N/A",
            "type" => EmployeeAddressType::PRESENT,
        ]);
        // employee permanent address
        $employee->permanent_address()->create([
            "street" => $jobApplicant->per_address_street ?: "N/A",
            "brgy" => $jobApplicant->per_address_brgy ?: "N/A",
            "city" => $jobApplicant->per_address_city ?: "N/A",
            "zip" => $jobApplicant->per_address_zip ?: "N/A",
            "province" => $jobApplicant->per_address_province ?: "N/A",
            "type" => EmployeeAddressType::PERMANENT,
        ]);
        // employee related person details
        $employeeRelatedPerson = $this->employeeRelatedPersonDetails();
        $employee->employee_related_person()->createMany($employeeRelatedPerson);
        if ($this->jobapplicantonly->workexperience) {
            $externalWorkExp = collect($this->jobapplicantonly->workexperience)->map(function ($workExp) {
                $dateFrom = Carbon::parse($workExp["inclusive_dates_from"])->format('Y-m-d');
                $dateTo = Carbon::parse($workExp["inclusive_dates_from"])->format('Y-m-d');
                return [
                    "date_from" => $dateFrom ?? null,
                    "date_to" => $dateTo ?? null,
                    "position_title" => $workExp["position_title"] ?? null,
                    "company_name" => $workExp["dpt_agency_office_company"] ?? null,
                    "salary" => is_numeric($workExp["monthly_salary"]) ? $workExp["monthly_salary"] : null,
                    "status_of_appointment" => $workExp["status_of_appointment"] ?? null,
                ];
            });
            $employee->employee_externalwork()->createMany($externalWorkExp);
        }
        //education
        if ($this->jobapplicantonly->education) {
            $employee->employee_education()->createMany(collect($this->jobapplicantonly->education)->toArray());
        }
        // children
        if ($this->jobapplicantonly->children) {
            $children = collect($this->jobapplicantonly->children)->map(function ($child) {
                $child["type"] = EmployeeRelatedPersonType::CHILD->value;
                return [
                    "name" => $child["name"],
                    "date_of_birth" => $child["birthdate"],
                    "type" => EmployeeRelatedPersonType::CHILD->value,
                ];
            })->toArray();
            $employee->employee_related_person()->createMany($children);
        }
        // update status for job appicants and manpower
        $this->jobapplicantonly()->update(["status" => JobApplicationStatusEnums::HIRED]);
        $this->jobapplicantonly->manpower()->update(["request_status" => ManpowerRequestStatus::FILLED]);
    }

    private function employeeRelatedPersonDetails(): array
    {
        $employeeRelatedPerson = [];
        $jobApplicant = $this->jobapplicantonly;
        if ($jobApplicant->name_of_spouse) {
            array_push($employeeRelatedPerson, [
                "name" => $jobApplicant->name_of_spouse,
                "date_of_birth" => $jobApplicant->date_of_birth_spouse ?? null,
                "contact_no" => $jobApplicant->telephone_spouse ?? null,
                "occupation" => $jobApplicant->occupation_spouse ?? null,
                "type" => EmployeeRelatedPersonType::SPOUSE,
            ]);
        }

        if ($jobApplicant->father_name) {
            array_push($employeeRelatedPerson, [
                "name" => $jobApplicant->father_name,
                "type" => EmployeeRelatedPersonType::FATHER,
            ]);
        }

        if ($jobApplicant->mother_name) {
            array_push($employeeRelatedPerson, [
                "name" => $jobApplicant->mother_name,
                "type" => EmployeeRelatedPersonType::MOTHER,
            ]);
        }

        if ($jobApplicant->icoe_name) {
            array_push($employeeRelatedPerson, [
                "name" => $jobApplicant->icoe_name ?? null,
                "street" => $jobApplicant->icoe_street ?? null,
                "brgy" => $jobApplicant->icoe_brgy ?? null,
                "city" => $jobApplicant->icoe_city ?? null,
                "zip" => $jobApplicant->icoe_zip ?? null,
                "province" => $jobApplicant->icoe_province ?? null,
                "relationship" => $jobApplicant->icoe_relationship ?? null,
                "contact_no" => $jobApplicant->telephone_icoe ?? null,
                "occupation" => $jobApplicant->telephone_icoe ?? null,
                "type" => EmployeeRelatedPersonType::CONTACT_PERSON
            ]);
        }
        return $employeeRelatedPerson;
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
        $interWorkExp->date_to = $this->date_of_effictivity;
        $interWorkExp->save();

        $newInterWorkExp = $interWorkExp->toArray();
        unset($newInterWorkExp["id"]);
        $newInterWorkExp['work_location'] = $this->work_location ?? $interWorkExp->work_location;
        if ($newInterWorkExp['work_location'] === WorkLocation::OFFICE->value) {
            $newInterWorkExp['department_id'] = $this->section_department_id ?? $interWorkExp->department_id;
        }
        $newInterWorkExp['date_from'] = $this->date_of_effictivity;
        $newInterWorkExp['salary_type'] = $this->salary_type;
        $newInterWorkExp['date_to'] = null;
        $newInterWorkExp['status'] = EmployeeInternalWorkExperiencesStatus::CURRENT;
        $newWorkExp = InternalWorkExperience::create($newInterWorkExp);
        if ($this->work_location === WorkLocation::PROJECT->value) {
            $newWorkExp->projects()->attach($this->project_ids);
        }
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
        $interWorkExp->date_to = $this->date_of_effictivity;
        $interWorkExp->save();

        $newInterWorkExp = $interWorkExp->toArray();
        unset($newInterWorkExp["id"]);
        $newInterWorkExp['position_id'] = $this->designation_position ?? $interWorkExp->position_id;
        $newInterWorkExp['employment_status'] = $this->employment_status ?? $interWorkExp->employment_status;
        $newInterWorkExp['salary_grades'] = $this->salary_grades ?? $interWorkExp->salary_grades;
        $newInterWorkExp['actual_salary'] = $this->salarygrade?->monthly_salary_amount ?? $interWorkExp->actual_salary;
        $newInterWorkExp['date_from'] = $this->date_of_effictivity;
        $newInterWorkExp['salary_type'] = $this->salary_type;
        $newInterWorkExp['date_to'] = null;
        $newInterWorkExp['status'] = EmployeeInternalWorkExperiencesStatus::CURRENT;
        InternalWorkExperience::create($newInterWorkExp);
    }
    /** Termination Employee PAN  request approved
     * to terminate proccessed.
     * Get InternalworkExperience related in PAN Request with the specific employee_id
     * Fill InternalWorkExperience model attributes from PAN Request and save.
     */
    public function terminationRequest()
    {
        $companyEmployment = $this->employee->company_employments;
        $interWorkExp = $this->getInternalWorkExp($this->employee_id);
        $interWorkExp->date_to = $this->date_of_effictivity;
        $interWorkExp->status = EmployeeInternalWorkExperiencesStatus::PREVIOUS;
        $interWorkExp->save();
        $companyEmployment->update([
            'status' => EmployeeCompanyEmploymentsStatus::INACTIVE->value
        ]);

        Termination::create([
            'employee_id' => $this->employee_id,
            'type_of_termination' => $this->type_of_termination,
            'reason_for_termination' => $this->reasons_for_termination,
            'eligible_for_rehire' => $this->eligible_for_rehire,
            'last_day_worked' => $this->last_day_worked,
        ]);
    }
    public function rehire()
    {
        // JUST A PLACEHOLDER WILL PROBABLY BE USED SOON
    }
}

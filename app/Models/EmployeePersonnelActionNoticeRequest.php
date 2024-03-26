<?php

namespace App\Models;

use App\Enums\PersonelAccessForm;
use App\Traits\HasApproval;
use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class EmployeePersonnelActionNoticeRequest extends Model
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasApproval, HasUser;

    const NEW_HIRE = "New Hire";
    const TRANSFER = "Transfer";
    const PROMOTION = "Promotion";
    const TERMINATION = "Termination";


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
        // switch ($this->type) {
        //     case EmployeePersonnelActionNoticeRequest::NEW_HIRE:
        //         $panRequestService->toHireEmployee($panRequest);
        //         break;
        //     case EmployeePersonnelActionNoticeRequest::TRANSFER:
        //         $panRequestService->toTransferEmployee($panRequest);
        //         break;
        //     case EmployeePersonnelActionNoticeRequest::PROMOTION:
        //         $panRequestService->toPromoteEmployee($panRequest);
        //         break;
        //     case EmployeePersonnelActionNoticeRequest::TERMINATION:
        //         $panRequestService->toTerminateEmployee($panRequest);
        //         break;
        // }
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

    public function requestStatusCompleted() : bool
    {
        if($this->request_status == PersonelAccessForm::REQUESTSTATUS_APPROVED){
            return true;
        }
        return false;
    }

    public function requestStatusEnded() : bool
    {
        if(
            in_array(
                $this->request_status,
                [
                    PersonelAccessForm::REQUESTSTATUS_DISAPPROVED,
                    PersonelAccessForm::REQUESTSTATUS_FILLED,
                    PersonelAccessForm::REQUESTSTATUS_HOLD,
                    PersonelAccessForm::REQUESTSTATUS_CANCELLED,
                ]
            )
        ){
            return true;
        }
        return false;
    }
}

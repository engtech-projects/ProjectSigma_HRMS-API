<?php

namespace App\Models;

use App\Traits\HasApproval;
use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class EmployeePersonnelActionNoticeRequest extends Model
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasApproval, HasUser;

    protected $appends = [
        'fullname'
    ];

    protected $casts = [
        'approvals' => 'array'
    ];

    public function getFullNameAttribute()
    {
        if ($this->type == "New Hire") {
            return $this->jobapplicant->lastname . ", " . $this->jobapplicant->firstname . " " . $this->jobapplicant->middlename;
        } else {
            return $this->employee->family_name . ", " . $this->employee->first_name . " " . $this->employee->middle_name;
        }
    }

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

    public function scopeApproval($query)
    {
        return $query->where("request_status", "=", "Pending");
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
}

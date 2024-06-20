<?php

namespace App\Models;

use App\Enums\EmployeeAddressType;
use Carbon\Carbon;
use App\Enums\EmployeeUploadType;
use Laravel\Sanctum\HasApiTokens;
use App\Enums\EmployeeStudiesType;
use Illuminate\Database\Eloquent\Model;
use App\Enums\EmployeeRelatedPersonType;
use App\Enums\RequestStatusType;
use App\Http\Traits\Attendance;
use App\Models\Traits\HasProjectEmployee;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Traits\EmployeeDTR;
use App\Models\Traits\EmployeePayroll;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class Employee extends Model
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use HasProjectEmployee;
    use EmployeeDTR;
    use EmployeePayroll;
    use Attendance;

    protected $table = 'employees';
    protected $appends = [
        'fullname_last',
        'fullname_first',
    ];

    protected function age(): Attribute
    {
        if (!$this->date_of_birth) {
            return new Attribute(
                get: fn () => "Date of birth not set.",
            );
        }
        return new Attribute(
            get: fn () => Carbon::createFromFormat("ymd", $this->date_of_birth->format('ymd'))->age,
        );
    }

    protected function fullnameLast(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->family_name . ", " . $this->first_name . " " . $this->middle_name
                . " " . $this->name_suffix,
        );
    }

    protected function fullnameFirst(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->first_name . " " . $this->middle_name . " " . $this->family_name
                . " " . $this->name_suffix,
        );
    }

    public function getLeaveCreditsAttribute()
    {
        $leaves_types = Leave::get();
        foreach ($leaves_types as $leavetype) {
            $leavetype->credits = $leavetype->amt_of_leave;
            if (!collect($leavetype->employment_status)->contains($this->current_employment->employment_status)) {
                $leavetype->credits = 0;
            }
            $leavetype->used = $this->employee_leave()
                ->where("leave_id", $leavetype->id)
                ->whereYear("date_of_absence_from", Carbon::now()->year)
                ->withPayLeave()
                ->approved()
                ->sum("number_of_days");
            $leavetype->balance = $leavetype->credits - $leavetype->used;
        }
        return $leaves_types;
    }

    protected $casts = [
        'date_of_birth' => 'datetime:Y-m-d',
        'date_of_marriage' => 'datetime:Y-m-d',
        'spouse_datebirth' => 'datetime:Y-m-d',
    ];

    public const EMPLOYEE_BULK_STATUS_DUPLICATE = 'duplicate';
    public const EMPLOYEE_BULK_STATUS_UNDUPLICATE = 'unduplicate';
    protected $fillable = [
        'first_name',
        'middle_name',
        'family_name',
        'name_suffix',
        'nick_name',
        'gender',
        'date_of_birth',
        'place_of_birth',
        'citizenship',
        'blood_type',
        'civil_status',
        'date_of_marriage',
        'telephone_number',
        'mobile_number',
        'email',
        'religion',
        'weight',
        'height',
    ];

    public function images()
    {
        return $this->morphMany(Image::class, 'parentable');
    }
    public function profile_photo()
    {
        return $this->morphOne(Image::class, 'parentable')->where('image_type', 'profile_image');
    }

    public function digital_signature()
    {
        return $this->morphOne(Image::class, 'parentable')->where('image_type', 'signature');
    }
    public function company_employments(): HasOne
    {
        return $this->hasOne(CompanyEmployee::class);
    }

    public function employment_records(): HasMany
    {
        return $this->hasMany(EmployeeRecord::class);
    }

    public function employee_address(): HasMany
    {
        return $this->hasMany(EmployeeAddress::class);
    }

    public function present_address(): HasOne
    {
        return $this->hasOne(EmployeeAddress::class)
            ->where("type", EmployeeAddressType::PRESENT)
            ->withDefault();
    }

    public function permanent_address(): HasOne
    {
        return $this->hasOne(EmployeeAddress::class)
            ->where("type", EmployeeAddressType::PERMANENT)
            ->withDefault();
    }

    public function current_employment(): HasOne
    {
        return $this->hasOne(InternalWorkExperience::class, 'employee_id')->where("status", "=", "current")
            ->with("employee_salarygrade.salary_grade_level", "employee_department");
    }

    public function employee_internal(): HasMany
    {
        return $this->hasMany(InternalWorkExperience::class)
            ->with("employee_salarygrade", "employee_department");
    }

    public function employee_salarygrade(): HasOne
    {
        return $this->hasOne(SalaryGradeStep::class);
    }

    public function cash_advance(): HasMany
    {
        return $this->hasMany(CashAdvance::class, 'employee_id');
    }

    public function other_deduction(): HasMany
    {
        return $this->hasMany(OtherDeduction::class, 'employee_id');
    }

    public function employee_department(): HasOne
    {
        return $this->hasOne(InternalWorkExperience::class, "id", "department_id");
    }

    public function employee_affiliation(): HasMany
    {
        return $this->hasMany(EmployeeAffiliation::class);
    }

    public function employee_education(): HasMany
    {
        return $this->hasMany(EmployeeEducation::class);
    }

    public function employee_education_elementary(): HasOne
    {
        return $this->hasOne(EmployeeEducation::class)->where("type", "elementary");
        // return $this->hasMany(EmployeeEducation::class)->select('elementary_name','elementary_education','elementary_period_attendance_to','elementary_period_attendance_from','elementary_year_graduated');
    }

    public function employee_education_secondary(): HasOne
    {
        return $this->hasOne(EmployeeEducation::class)->where("type", "secondary");
        // return $this->hasMany(EmployeeEducation::class)->select('secondary_name','secondary_education','secondary_period_attendance_to','secondary_period_attendance_from','secondary_year_graduated');
    }

    public function employee_education_vocationalcourse(): HasOne
    {
        return $this->hasOne(EmployeeEducation::class)->where("type", "vocational_course");
        // return $this->hasMany(EmployeeEducation::class)->select('vocationalcourse_name','vocationalcourse_education','vocationalcourse_period_attendance_to','vocationalcourse_period_attendance_from','vocationalcourse_year_graduated');
    }

    public function employee_education_college(): HasOne
    {
        return $this->hasOne(EmployeeEducation::class)->where("type", "college");
        // return $this->hasMany(EmployeeEducation::class)->select('college_name','college_education','college_period_attendance_to','college_period_attendance_from','college_year_graduated');
    }
    public function employee_education_graduatestudies(): HasOne
    {
        return $this->hasOne(EmployeeEducation::class)->where("type", "graduate_studies");
        // return $this->hasMany(EmployeeEducation::class)->select('graduatestudies_name','graduatestudies_education','graduatestudies_period_attendance_to','graduatestudies_period_attendance_from','graduatestudies_year_graduated');
    }

    public function employee_eligibility(): HasMany
    {
        return $this->hasMany(EmployeeEligibility::class);
    }

    public function employee_seminartraining(): HasMany
    {
        return $this->hasMany(EmployeeSeminartraining::class);
    }

    public function employee_related_person(): HasMany
    {
        return $this->hasMany(EmployeeRelatedperson::class);
    }

    public function employee_externalwork(): HasMany
    {
        return $this->hasMany(ExternalWorkExperience::class);
    }

    public function mother(): HasOne
    {
        $a = $this->hasOne(EmployeeRelatedperson::class)->where('type', "=", EmployeeRelatedPersonType::MOTHER)->withDefault();
        return $a;
    }

    public function father(): HasOne
    {
        return $this->hasOne(EmployeeRelatedperson::class)->where('type', "=", EmployeeRelatedPersonType::FATHER)->withDefault();
    }

    public function contact_person(): HasOne
    {
        return $this->hasOne(EmployeeRelatedperson::class)->where('type', "=", EmployeeRelatedPersonType::CONTACT_PERSON)->withDefault();
    }
    public function guardian(): HasOne
    {
        return $this->hasOne(EmployeeRelatedperson::class)->where('type', "=", EmployeeRelatedPersonType::GUARDIAN);
    }
    public function spouse(): HasOne
    {
        return $this->hasOne(EmployeeRelatedperson::class)->where('type', "=", EmployeeRelatedPersonType::SPOUSE);
    }

    public function reference(): HasMany
    {
        return $this->hasMany(EmployeeRelatedperson::class)->where('type', "=", EmployeeRelatedPersonType::REFERENCE);
    }

    public function employee_studies(): HasMany
    {
        return $this->hasMany(EmployeeStudies::class);
    }

    public function masterstudies(): HasOne
    {
        return $this->hasOne(EmployeeStudies::class)->where('type', "=", EmployeeStudiesType::MASTER)->withDefault();
    }

    public function doctorstudies(): HasOne
    {
        return $this->hasOne(EmployeeStudies::class)->where('type', "=", EmployeeStudiesType::DOCTOR)->withDefault();
    }

    public function professionalstudies(): HasOne
    {
        return $this->hasOne(EmployeeStudies::class)->where('type', "=", EmployeeStudiesType::PROFESSIONAL)->withDefault();
    }

    public function child(): HasMany
    {
        return $this->hasMany(EmployeeRelatedperson::class)->where('type', "=", EmployeeRelatedPersonType::CHILD);
    }

    public function memo(): HasMany
    {
        return $this->hasMany(EmployeeUploads::class)->where('upload_type', "=", EmployeeUploadType::MEMO);
    }

    public function docs(): HasMany
    {
        return $this->hasMany(EmployeeUploads::class)->where('upload_type', "=", EmployeeUploadType::DOCUMENTS);
    }

    public function account(): HasOne
    {
        return $this->hasOne(Users::class);
    }

    public function employee_schedule(): HasMany
    {
        return $this->hasMany(Schedule::class, 'employee_id');
    }

    public function attendance_log(): HasMany
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function employee_loan(): HasMany
    {
        return $this->hasMany(Loans::class);
    }

    public function employee_leave(): HasMany
    {
        return $this->hasMany(EmployeeLeaves::class, 'employee_id')
            ->where("request_status", RequestStatusType::APPROVED);
    }

    public function face_patterns(): HasMany
    {
        return $this->hasMany(EmployeePattern::class);
    }

    public function employee_travel_order(): BelongsToMany
    {
        return $this->belongsToMany(TravelOrder::class, 'travel_order_members', 'employee_id');
    }

    public function employee_overtime(): BelongsToMany
    {
        return $this->belongsToMany(Overtime::class, 'overtime_employees', 'employee_id');
    }

    public function employee_has_overtime(): BelongsToMany
    {
        return $this->belongsToMany(Overtime::class, 'overtime_employees', 'id', 'employee_id')
            ->withtimestamps();
    }
    public function applied_schedule($date)
    {
        $schedule = $this->employee_schedule()?->schedulesOnDay($date)->irregularSchedules()->get();
        if ($schedule && sizeof($schedule) > 0) {
            return $schedule;
        }
        $schedule = $this->employee_schedule()?->schedulesOnDay($date)->regularSchedules()->get();
        if ($schedule && sizeof($schedule) > 0) {
            return $schedule;
        }
        $schedule = $this->employee_has_projects()?->orderBy('id', 'desc')->first()?->project_schedule()?->schedulesOnDay($date)->irregularSchedules()->get();
        if ($schedule && sizeof($schedule) > 0) {
            return $schedule;
        }
        $schedule = $this->employee_has_projects()?->orderBy('id', 'desc')->first()?->project_schedule()?->schedulesOnDay($date)->regularSchedules()->get();
        if ($schedule && sizeof($schedule) > 0) {
            return $schedule;
        }
        $schedule = $this->employee_internal()?->currentOnDate($date)?->first()->employee_department?->schedule()?->schedulesOnDay($date)->irregularSchedules()->get();
        if ($schedule && sizeof($schedule) > 0) {
            return $schedule;
        }
        $schedule = $this->employee_internal()?->currentOnDate($date)?->first()->employee_department?->schedule()?->schedulesOnDay($date)->regularSchedules()->get();
        if ($schedule && sizeof($schedule) > 0) {
            return $schedule;
        }
        return collect([]); // returns collection of empty array if no schedule is found
    }

    public function applied_schedule_with_attendance($date)
    {
        $schedWithLogs =  $this->applied_schedule($date);
        return $schedWithLogs->map(function ($sched) use ($date) {
            return [
                ...$sched->toArray(),
                "applied_ins" => $sched->attendance_log_ins?->where("employee_id", $this->id)->where("date", $date)->first(),
                "applied_outs" => $sched->attendance_log_outs?->where("employee_id", $this->id)->where("date", $date)->last()
            ];
        });

    }

    public function applied_overtime_with_attendance($date)
    {
        $otSchedWithLogs =  $this->employee_overtime()->whereDate('overtime_date', "=", $date)->get();
        return $otSchedWithLogs->map(function ($sched) use ($date) {
            return [
                ...$sched->toArray(),
                "applied_in" => $sched->attendance_log_ins?->where("employee_id", $this->id)->first(),
                "applied_out" => $sched->attendance_log_outs?->where("employee_id", $this->id)->last()
            ];
        });

    }

    public function daily_attendance_schedule($date)
    {
        return $this->attendance_log()->whereDate("date", $date)->orderBy('time')->get();
    }

    public function filter_employee_schedule($start_range, $end_range)
    {
        $data = Schedule::select('employee_id', 'startTime', 'endTime', 'startRecur', 'endRecur')->where([
            ['startRecur', '>=', $start_range],
            ['endRecur', '<=', $end_range],
        ])->with([
            'employee' => function ($query) {
                return $query->select(['first_name', 'middle_name', 'family_name', 'id']);
            }
        ])->addSelect(DB::raw('startTime as late'))->get();
    }
}

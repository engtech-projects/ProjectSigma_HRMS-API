<?php

namespace App\Models;

use Carbon\Carbon;
use App\Enums\EmployeeUploadType;
use Laravel\Sanctum\HasApiTokens;
use App\Enums\EmployeeStudiesType;
use Illuminate\Database\Eloquent\Model;
use App\Enums\EmployeeRelatedPersonType;
use App\Models\Traits\HasProjectEmployee;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class Employee extends Model
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use HasProjectEmployee;

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

    public function current_employment(): HasOne
    {
        return $this->hasOne(InternalWorkExperience::class)->where("status", "=", "current")
            ->with("employee_salarygrade", "employee_department");
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
        return $this->hasOne(EmployeeRelatedperson::class)->where('type', "=", EmployeeRelatedPersonType::CONTACT_PERSON);
    }
    public function guardian(): HasOne
    {
        return $this->hasOne(EmployeeRelatedperson::class)->where('type', "=", EmployeeRelatedPersonType::GUARDIAN);
    }
    public function spouse(): HasOne
    {
        return $this->hasOne(EmployeeRelatedperson::class)->where('type', "=", EmployeeRelatedPersonType::SPOUSE);
    }

    public function reference(): HasOne
    {
        return $this->hasOne(EmployeeRelatedperson::class)->where('type', "=", EmployeeRelatedPersonType::REFERENCE);
    }

    public function employee_studies(): HasMany
    {
        return $this->hasMany(EmployeeStudies::class);
    }

    public function masterstudies(): HasOne
    {
        return $this->hasOne(EmployeeStudies::class)->where('type', "=", EmployeeStudiesType::MASTER);
    }

    public function doctorstudies(): HasOne
    {
        return $this->hasOne(EmployeeStudies::class)->where('type', "=", EmployeeStudiesType::DOCTOR);
    }

    public function professionalstudies(): HasOne
    {
        return $this->hasOne(EmployeeStudies::class)->where('type', "=", EmployeeStudiesType::PROFESSIONAL);
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
        return $this->hasMany(Schedule::class);
    }

    public function attendance_log(): HasMany
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function employee_leave(): HasMany
    {
        return $this->hasMany(EmployeeLeaves::class);
    }

    public function employee_overtime(): BelongsToMany
    {
        return $this->belongsToMany(Overtime::class, 'overtime_employees');
    }

    public function employee_has_overtime(): BelongsToMany
    {
        return $this->belongsToMany(Overtime::class, 'overtime_employees', 'id', 'employee_id')
            ->withtimestamps();
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

    public function dtrSchedule($employee, $date)
    {
        $schedule = $employee->employee_schedule()
            ->where('startRecur', $date)
            ->get();
        return $schedule;
    }
    public function dtrAttendance($employee, $date)
    {
        $attendance = $employee->attendance_log()
            ->where('date', $date)
            ->get();
        return $attendance;
    }
    public function dtrOvertime($employee, $date)
    {
        $overtime = $employee->employee_overtime()
            ->where('overtime_date', $date)
            ->approved()
            ->get();
        return $overtime;
    }

    public function dtrEvents($date)
    {
        return Events::whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->get();
    }
    public function dtrLeave($date)
    {
        return $this->employee_leave()
            ->whereDate('date_of_absence_from', '<=', $date)
            ->whereDate('date_of_absence_to', '>=', $date)
            ->get();
    }
}

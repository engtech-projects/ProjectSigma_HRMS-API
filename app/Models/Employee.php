<?php

namespace App\Models;

use Carbon\Carbon;
use App\Enums\EmployeeUploadType;
use Laravel\Sanctum\HasApiTokens;
use App\Enums\EmployeeStudiesType;
use Illuminate\Database\Eloquent\Model;
use App\Enums\EmployeeRelatedPersonType;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\EmployeeInternalWorkExperiencesStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'employees';
    protected $appends = [
        'fullname_last',
        'fullname_first',
    ];

    protected function age(): Attribute
    {
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

    protected $casts = [
        'date_of_birth' => 'datetime:Y-m-d',
        'date_of_marriage' => 'datetime:Y-m-d',
        'spouse_datebirth' => 'datetime:Y-m-d',
    ];

    const EMPLOYEE_BULK_STATUS_DUPLICATE = 'duplicate';
    const EMPLOYEE_BULK_STATUS_UNDUPLICATE = 'unduplicate';
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

    public function employee_internal(): HasOne
    {
        return $this->hasOne(InternalWorkExperience::class)->where("status", "=", "current")
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

    public function employee_education_elementary(): HasMany
    {
        return $this->hasMany(EmployeeEducation::class)->where("type", "elementary");
        // return $this->hasMany(EmployeeEducation::class)->select('elementary_name','elementary_education','elementary_period_attendance_to','elementary_period_attendance_from','elementary_year_graduated');
    }

    public function employee_education_secondary(): HasMany
    {
        return $this->hasMany(EmployeeEducation::class)->where("type", "secondary");
        // return $this->hasMany(EmployeeEducation::class)->select('secondary_name','secondary_education','secondary_period_attendance_to','secondary_period_attendance_from','secondary_year_graduated');
    }

    public function employee_education_vocationalcourse(): HasMany
    {
        return $this->hasMany(EmployeeEducation::class)->where("type", "vocational_course");
        // return $this->hasMany(EmployeeEducation::class)->select('vocationalcourse_name','vocationalcourse_education','vocationalcourse_period_attendance_to','vocationalcourse_period_attendance_from','vocationalcourse_year_graduated');
    }

    public function employee_education_college(): HasMany
    {
        return $this->hasMany(EmployeeEducation::class)->where("type", "college");
        // return $this->hasMany(EmployeeEducation::class)->select('college_name','college_education','college_period_attendance_to','college_period_attendance_from','college_year_graduated');
    }
    public function employee_education_graduatestudies(): HasMany
    {
        return $this->hasMany(EmployeeEducation::class)->where("type", "graduate_studies");
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
        $a = $this->hasOne(EmployeeRelatedperson::class)->where('type', "=", EmployeeRelatedPersonType::MOTHER);
        return $a;
    }

    public function father(): HasOne
    {
        return $this->hasOne(EmployeeRelatedperson::class)->where('type', "=", EmployeeRelatedPersonType::FATHER);
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

    public function scopeUser($query, $id)
    {
        return Users::where("id", $id)->first();
    }



}

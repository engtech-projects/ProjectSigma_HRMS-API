<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Enums\EmployeeRelatedPersonType;
use App\Enums\EmployeeUploadType;

class Employee extends Model
{
    use HasApiTokens, HasFactory, Notifiable,SoftDeletes;
    protected $casts = [
        'date_of_birth' => 'datetime:Y-m-d',
        'date_of_marriage' => 'datetime:Y-m-d',
        'spouse_datebirth' => 'datetime:Y-m-d',
    ];
    const EMPLOYEE_BULK_STATUS_DUPLICATE = 'duplicate';
    const EMPLOYEE_BULK_STATUS_UNDUPLICATE = 'unduplicate';
    protected $fillable = [
        'id',
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
        'pre_street',
        'pre_brgy',
        'pre_city',
        'pre_zip',
        'pre_province',
        'per_street',
        'per_brgy',
        'per_city',
        'per_zip',
        'per_province',
        'father_name',
        'mother_name',
        'spouse_name',
        'spouse_datebirth',
        'spouse_occupation',
        'spouse_contact_no',
        'childrens',
        'person_to_contact_name',
        // 'person_to_contact_address',
        'person_to_contact_street',
        'person_to_contact_brgy',
        'person_to_contact_city',
        'person_to_contact_zip',
        'person_to_province',
        'person_to_contact_no',
        'person_to_contact_relationship',
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

    public function employee_affiliation(): HasMany
    {
        return $this->hasMany(EmployeeAffiliation::class);
    }

    public function employee_education(): HasMany
    {
        return $this->hasMany(EmployeeEducation::class);
    }

    public function employee_eligibility(): HasMany
    {
        return $this->hasMany(EmployeeEligibility::class);
    }

    public function employee_seminartraining(): HasMany
    {
        return $this->hasMany(EmployeeSeminartraining::class);
    }

    public function mother(): HasOne
    {
        return $this->hasOne(EmployeeRelatedperson::class)->where('type',"=",EmployeeRelatedPersonType::MOTHER);
    }

    public function father(): HasOne
    {
        return $this->hasOne(EmployeeRelatedperson::class)->where('type',"=",EmployeeRelatedPersonType::FATHER);
    }

    public function contact_person(): HasOne
    {
        return $this->hasOne(EmployeeRelatedperson::class)->where('type',"=",EmployeeRelatedPersonType::CONTACT_PERSON);
    }

    public function spouse(): HasOne
    {
        return $this->hasOne(EmployeeRelatedperson::class)->where('type',"=",EmployeeRelatedPersonType::SPOUSE);
    }

    public function reference(): HasOne
    {
        return $this->hasOne(EmployeeRelatedperson::class)->where('type',"=",EmployeeRelatedPersonType::REFERENCE);
    }

    public function guardian(): HasOne
    {
        return $this->hasOne(EmployeeRelatedperson::class)->where('type',"=",EmployeeRelatedPersonType::GUARDIAN);
    }

    public function child(): HasMany
    {
        return $this->hasMany(EmployeeRelatedperson::class)->where('type',"=",EmployeeRelatedPersonType::CHILD);
    }

    public function memo(): HasMany
    {
        return $this->hasMany(EmployeeUploads::class)->where('upload_type',"=",EmployeeUploadType::MEMO);
    }
    public function docs(): HasMany
    {
        return $this->hasMany(EmployeeUploads::class)->where('upload_type',"=",EmployeeUploadType::DOCUMENTS);
    }
}

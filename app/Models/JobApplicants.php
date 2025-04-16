<?php

namespace App\Models;

use App\Enums\EmployeeEducationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class JobApplicants extends Model
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;


    protected $fillable = [
        'application_letter_attachment',
        'resume_attachment',
        'firstname',
        'middlename',
        'lastname',
        'name_suffix',
        'nickname',
        'date_of_application',
        'date_of_birth',
        'pre_address_street',
        'pre_address_brgy',
        'pre_address_city',
        'pre_address_zip',
        'pre_address_province',
        'per_address_street',
        'per_address_brgy',
        'per_address_city',
        'per_address_zip',
        'per_address_province',
        'contact_info',
        'email',
        'how_did_u_learn_about_our_company',
        'desired_position',
        'currently_employed',
        'name_of_spouse',
        'date_of_birth_spouse',
        'occupation_spouse',
        'telephone_spouse',
        'children',
        'icoe_name',
        'icoe_address',
        'icoe_relationship',
        'icoe_occupation',
        'icoe_date_of_birth',
        'telephone_icoe',
        'workexperience',
        'education',
        'place_of_birth',
        'blood_type',
        'date_of_marriage',
        'sss',
        'philhealth',
        'pagibig',
        'tin',
        'atm',
        'citizenship',
        'religion',
        'height',
        'weight',
        'father_name',
        'mother_name',
        'gender',
        'civil_status',
        'icoe_street',
        'icoe_brgy',
        'icoe_city',
        'icoe_zip',
        'icoe_province',
        'status',
        'remarks',
    ];


    public $casts = [
        "children" => "array",
        "education" => "array",
        "workexperience" => "array",
    ];

    protected $appends = [
        'fullname_last',
        'fullname_first',
    ];

    public function manpower(): BelongsToMany
    {
        return $this->belongsToMany(ManpowerRequest::class, 'manpower_request_job_applicants', 'job_applicants_id', 'manpowerrequests_id')->withPivot("hiring_status", "processing_checklist", "remarks");
    }


    public function manpowerRequestJobApplicants(): HasMany
    {
        return $this->hasMany(ManpowerRequestJobApplicants::class, 'job_applicants_id', 'id');
    }

    protected function getFullnameLastAttribute()
    {
        return $this->lastname . ", " . $this->firstname . " " . $this->middlename;
    }

    protected function getFullnameFirstAttribute()
    {
        return $this->firstname . " " . $this->middlename . " " . $this->lastname;
    }

    public function getEducationElementaryAttribute()
    {
        return collect($this->education)->where("type", EmployeeEducationType::ELEMENTARY->value)->first() ?? [
            "type" => EmployeeEducationType::ELEMENTARY->value,
            "name" => "",
            "education" => "",
            "year_graduated" => "",
            "honors_received" => "",
            "period_attendance_to" => "",
            "period_attendance_from" => "",
            "degree_earned_of_school" => "",
        ];
    }

    public function getEducationSecondaryAttribute()
    {
        return collect($this->education)->where("type", EmployeeEducationType::SECONDARY->value)->first() ?? [
            "type" => EmployeeEducationType::SECONDARY->value,
            "name" => "",
            "education" => "",
            "year_graduated" => "",
            "honors_received" => "",
            "period_attendance_to" => "",
            "period_attendance_from" => "",
            "degree_earned_of_school" => "",
        ];
    }

    public function getEducationVocationalAttribute()
    {
        return collect($this->education)->where("type", EmployeeEducationType::VOCATIONAL->value)->first() ?? [
            "type" => EmployeeEducationType::VOCATIONAL->value,
            "name" => "",
            "education" => "",
            "year_graduated" => "",
            "honors_received" => "",
            "period_attendance_to" => "",
            "period_attendance_from" => "",
            "degree_earned_of_school" => "",
        ];
    }

    public function getEducationCollegeAttribute()
    {
        return collect($this->education)->where("type", EmployeeEducationType::COLLEGE->value)->first() ?? [
            "type" => EmployeeEducationType::COLLEGE->value,
            "name" => "",
            "education" => "",
            "year_graduated" => "",
            "honors_received" => "",
            "period_attendance_to" => "",
            "period_attendance_from" => "",
            "degree_earned_of_school" => "",
        ];
    }

    public function getEducationGraduateAttribute()
    {
        return collect($this->education)->where("type", EmployeeEducationType::GRADUATE_STUDIES->value)->first() ?? [
            "type" => EmployeeEducationType::GRADUATE_STUDIES->value,
            "name" => "",
            "education" => "",
            "year_graduated" => "",
            "honors_received" => "",
            "period_attendance_to" => "",
            "period_attendance_from" => "",
            "degree_earned_of_school" => "",
        ];
    }

}

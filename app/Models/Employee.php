<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employee extends Model
{
    use HasApiTokens, HasFactory, Notifiable,SoftDeletes;

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
        'curr_address',
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
        'person_to_contact_address',
        'person_to_contact_no',
        'person_to_contact_relationship',
        'person_to_contact_relationship',
    ];

    public function company_employments(): HasOne
    {
        return $this->hasOne(CompanyEmployee::class);
    }

    public function employment_records(): HasMany
    {
        return $this->hasMany(EmployeeRecord::class);
    }
}

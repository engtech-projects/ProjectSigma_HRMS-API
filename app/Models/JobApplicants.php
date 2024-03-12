<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class JobApplicants extends Model
{
    use HasApiTokens, HasFactory, Notifiable,SoftDeletes;

    protected $fillable = [
        'id',
        'manpowerrequests_id',
        'name_suffix',
        'application_letter_attachment',
        'resume_attachment',
        'status',
        'lastname',
        'firstname',
        'middlename',
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
    ];

    public function manpower(): BelongsTo
    {
        return $this->belongsTo(ManpowerRequest::class, 'manpowerrequests_id', 'id');
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class JobApplicants extends Model
{
    use HasApiTokens, HasFactory, Notifiable,SoftDeletes;

    protected $fillable = [
        'id',
        'manpowerrequests_id',
        'application_name',
        'application_letter_attachment',
        'resume_attachment',
        'status',
        'lastname',
        'firstname',
        'middlename',
        'date_of_application',
        'date_of_birth',
        'address_street',
        'address_city',
        'address_zip',
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
        'education'
    ];

}

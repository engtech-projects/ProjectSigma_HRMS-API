<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Employee extends Model
{
    use HasApiTokens, HasFactory, Notifiable,SoftDeletes;

    protected $fillable = [
        'id',
        'first_name',
        'middle_name',
        'family_name',
        'name_suffix',
        'gender',
        'date_of_birth',
        'place_of_birth',
        'citizenship',
        'blood_type',
        'civil_status',
        'telephone_number',
        'mobile_number',
        'email',
        'religion',
        'curr_address',
        'perm_address',
        'father_name',
        'mother_name',
        'spouse_datebirth',
        'spouse_occupation',
        'spouse_contact_no',
    ];
}

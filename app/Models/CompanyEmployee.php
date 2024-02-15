<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class CompanyEmployee extends Model
{
    use HasApiTokens, HasFactory, Notifiable,SoftDeletes;

    protected $fillable = [
        'id',
        'employee_id',
        'employeedisplay_id',
        'company',
        'date_hired',
        'employment_status',
        'position',
        'section_program',
        'department',
        'division',
        'imidiate_supervisor',
        'phic_number',
        'sss_number',
        'tin_number',
        'pagibig_number',
    ];
}

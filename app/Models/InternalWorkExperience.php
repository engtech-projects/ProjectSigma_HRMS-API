<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class InternalWorkExperience extends Model
{
    use HasApiTokens, HasFactory, Notifiable,SoftDeletes;
    protected $fillable = [
        'id',
        'employee_id',
        'position_title',
        'employment_status',
        'department',
        'immediate_supervisor',
        'salary_type',
        'salary_grade',
        'actual_salary',
        'work_location',
        'hire_source',
        'status',
        'date_from',
        'date_to'
    ];
}

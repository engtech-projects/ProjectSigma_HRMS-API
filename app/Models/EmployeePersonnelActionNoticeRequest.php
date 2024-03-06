<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class EmployeePersonnelActionNoticeRequest extends Model
{
    use HasApiTokens, HasFactory, Notifiable,SoftDeletes;

    protected $fillable = [
        'id',
        'employee_id',
        'type',
        'date_of_effictivity',
        'section_department',
        'designation_position',
        'salary_grade',
        'salary_grade_step',
        'salary_type',
        'hire_source',
        'work_location',
        'new_section',
        'new_location',
        'new_employment_status',
        'new_position',
        'new_salary_grade',
        'new_salary_grade_step',
        'type_of_termination',
        'reasons_for_termination',
        'eligible_for_rehire',
        'last_day_worked',
        'approvals',
        'created_by',
    ];
}

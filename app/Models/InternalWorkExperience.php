<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class InternalWorkExperience extends Model
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;
    protected $fillable = [
        'id',
        'employee_id',
        'position_title',
        'employment_status',
        'department',
        'immediate_supervisor',
        'actual_salary',
        'salary_grades',
        'work_location',
        'hire_source',
        'status',
        'date_from',
        'date_to'
    ];

    public function employee_salarygrade(): HasOne
    {
        return $this->hasOne(SalaryGradeStep::class, "id", "salary_grades");
    }
}

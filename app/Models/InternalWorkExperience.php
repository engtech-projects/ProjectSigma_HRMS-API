<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Enums\EmployeeInternalWorkExperiencesStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InternalWorkExperience extends Model
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;
    protected $fillable = [
        'id',
        'employee_id',
        'position_title',
        'employment_status',
        'department_id',
        'immediate_supervisor',
        'actual_salary',
        'salary_grades',
        'work_location',
        'hire_source',
        'status',
        'date_from',
        'date_to'
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($internalWorkExp) {
            $internalWorkExp->status = EmployeeInternalWorkExperiencesStatus::CURRENT;
            $internalWorkExp->date_to = null;
        });
    }

    public function employee_salarygrade(): HasOne
    {
        return $this->hasOne(SalaryGradeStep::class, "id", "salary_grades");
    }

    public function employee_department(): HasOne
    {
        return $this->hasOne(InternalWorkExperience::class, "id", "department_id");
    }
}

<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Enums\EmployeeInternalWorkExperiencesStatus;
use App\Enums\InternalWorkExpStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InternalWorkExperience extends Model
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    protected $fillable = [
        'id',
        'employee_id',
        'position_id',
        'position_title',
        'employment_status',
        'department_id',
        'immediate_supervisor',
        'actual_salary',
        'salary_grades',
        'salary_type',
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
        });
    }

    public function employee_salarygrade(): HasOne
    {
        return $this->hasOne(SalaryGradeStep::class, "salary_grade_level_id");
    }

    public function employee_department(): HasOne
    {
        return $this->hasOne(Department::class, "id", "department_id");
    }

    public function position(): HasOne
    {
        return $this->hasOne(Position::class, "id", "position_id");
    }


    public function employees()
    {
        return $this->hasMany(Employee::class, 'id', 'employee_id');
    }


    public function scopeByEmployee(Builder $query, $id): Builder
    {
        return $this->where('employee_id', $id);
    }

    public function scopeStatusCurrent(Builder $query): Builder
    {
        return $query->where('status', InternalWorkExpStatus::CURRENT);
    }
}

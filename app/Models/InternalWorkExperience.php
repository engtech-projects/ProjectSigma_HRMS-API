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
    public const EMPLOYEE_WORK_ASSIGNMENT = 'App\Model\EmployeeWorkAssignment';
    public static function boot()
    {
        parent::boot();
        static::creating(function ($internalWorkExp) {
            $internalWorkExp->status = EmployeeInternalWorkExperiencesStatus::CURRENT;
        });
    }

    public function employee_salarygrade(): HasOne
    {
        return $this->hasOne(SalaryGradeStep::class, "id", "salary_grades");
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
    public function department_schedule()
    {
        return $this->hasMany(Schedule::class, 'department_id', 'department_id');
    }
    public function irregular_department_schedule($date)
    {
        return $this->hasMany(Schedule::class, 'department_id', 'department_id')->whereDate('startRecur', $date);
    }
    public function regular_department_schedule($date)
    {
        return $this->hasMany(Schedule::class, 'department_id', 'department_id')->employeeSchedule($date);
    }
    public function regular_project_schedule()
    {
    }
    public function irregular_project_schedule($date)
    {
        return $this->hasMany(ProjectMember::class, 'project_id', 'project_id');
    }


    public function scopeByEmployee(Builder $query, $id): Builder
    {
        return $this->where('employee_id', $id);
    }

    public function scopeStatusCurrent(Builder $query): Builder
    {
        return $query->where('status', InternalWorkExpStatus::CURRENT);
    }

    public function scopeCurrentOnDate(Builder $query, $date): Builder
    {

        return $query->where(function ($query) use ($date) {
            $query->whereDate('date_from', '<=', $date)->whereNotNull('date_to')->whereDate('date_to', '>', $date);
        })->orWhere(function ($query) use ($date) {
            $query->whereDate('date_from', '<=', $date)->whereNull('date_to');
        });


        /* return $query->where(function ($query) use ($date) {
            $query->where('date_from', '>=', $date)
                ->orWhereNull('date_to');
        })->where('date_to', '<', $date); */
    }
    public function work_assignment()
    {
        return $this->morphedToMany(InternalWorkExperience::EMPLOYEE_WORK_ASSIGNMENT, 'work_assignment');
    }

}

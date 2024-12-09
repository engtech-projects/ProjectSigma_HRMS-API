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
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

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
    /**
    * ==================================================
    * MODEL RELATIONSHIPS
    * ==================================================
    */
    public function employee_salarygrade(): HasOne
    {
        return $this->hasOne(SalaryGradeStep::class, "id", "salary_grades")->withTrashed();
    }

    public function position(): HasOne
    {
        return $this->hasOne(Position::class, "id", "position_id")->withTrashed();
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'id', 'employee_id');
    }
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }
    public function department_schedule()
    {
        return $this->hasMany(Schedule::class, 'department_id', 'department_id');
    }
    public function department_schedule_regular()
    {
        return $this->hasMany(Schedule::class, 'department_id', 'department_id')->regularSchedules();
    }
    public function department_schedule_irregular()
    {
        return $this->hasMany(Schedule::class, 'department_id', 'department_id')->irregularSchedules();
    }
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, InternalWorkExperienceProjects::class)
            ->withTimestamps();
    }
    /**
    * ==================================================
    * MODEL ATTRIBUTES
    * ==================================================
    */
    public function getProjectNamesAttribute()
    {
        return $this->projects()->pluck('project_code');
    }
    public function getProjectIdsAttribute()
    {
        return $this->projects()->pluck('id');
    }
    public function getDepartmentNameAttribute()
    {
        return $this->department?->department_name ?? "";
    }
    /**
    * ==================================================
    * STATIC SCOPES
    * ==================================================
    */
    public function scopeStatusCurrent(Builder $query): Builder
    {
        return $query->where('status', InternalWorkExpStatus::CURRENT);
    }
    /**
    * ==================================================
    * DYNAMIC SCOPES
    * ==================================================
    */
    public function scopeByEmployee(Builder $query, $id): Builder
    {
        return $this->where('employee_id', $id);
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
    public function scopeBetweenDates(Builder $query, $dateFrom, $dateTo)
    {
        return $query->where(function ($query) use ($dateFrom, $dateTo) {
            $query->whereDate("date_from", "<=", $dateTo)
                ->where(function ($query) use ($dateFrom) {
                    $query->whereDate("date_to", ">=", $dateFrom)
                    ->orWhereNull('date_to');
                });
        });
    }
    /**
    * ==================================================
    * MODEL FUNCTIONS
    * ==================================================
    */

}

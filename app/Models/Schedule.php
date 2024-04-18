<?php

namespace App\Models;

use App\Enums\ScheduleGroupType;
use Carbon\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Schedule extends Model
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    protected $table = "schedules";

    protected $fillable = [
        'id',
        'groupType',
        'department_id',
        'project_id',
        'employee_id',
        'scheduleType',
        'daysOfWeek',
        'startTime',
        'endTime',
        'startRecur',
        'endRecur',
    ];

    protected $casts = [
        'startTime' => 'date:H:s:i',
        'endTime' => 'date:H:s:i',
        'startRecur' => 'date:Y-m-d',
        'endRecur' => 'date:Y-m-d',
    ];

    public function department(): HasOne
    {
        return $this->hasOne(Department::class, "id", "department_id");
    }
    public function project(): HasOne
    {
        return $this->hasOne(Project::class, "id", "project_id");
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, "id", "employee_id");
    }

    /**
     * MODEL
     * LOCAL
     * SCOPES
     */

    public function scopeEmployeeSchedule(Builder $query, array $filter = [])
    {
        $query->where(function ($query) use ($filter) {
            $query->where('startRecur', '>=', $filter['start_date'])
                ->orWhereNull('endRecur');
        })->where('endRecur', '<', $filter['end_date']);
    }

    public function scheduleEmployeeThisMonth($query)
    {
        return $query->with('employee')->where('groupType', ScheduleGroupType::EMPLOYEE)->whereBetween(
            'startRecur',
            [
                Carbon::now()->startOfMonth()->format('Y-m-d'),
                Carbon::now()->endOfMonth()->format('Y-m-d')
            ]
        )->get();
    }

    public function scheduleEmployeeDateFilter($query, $start_date, $end_date)
    {
        return $query->with('employee')->where('groupType', ScheduleGroupType::EMPLOYEE)->whereBetween(
            'startRecur',
            [
                $start_date,
                $end_date
            ]
        )->get();
    }
}

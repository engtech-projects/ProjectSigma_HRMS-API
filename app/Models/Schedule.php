<?php

namespace App\Models;

use App\Enums\AttendanceLogType;
use App\Enums\ScheduleGroupType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Schedule extends Model
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    public const TYPE_REGULAR = 'Regular';
    public const TYPE_IRREGULAR = 'Irregular';

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
        'daysOfWeek' => 'array',
        'startTime' => 'date:H:i',
        'endTime' => 'date:H:i',
        'startRecur' => 'date:Y-m-d',
        'endRecur' => 'date:Y-m-d',
    ];

    protected $appends = [
        'day_of_week_names_short',
        'day_of_week_names',
        'start_time_human',
        'end_time_human',
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

    public function employeesAssigned(): HasMany
    {
        if ($this->deparment_id) {
            return $this->department->employees();
        } elseif ($this->project_id) {
            return $this->project->project_has_employees();
        } else {
            return $this->hasMany(Employee::class, "id", "employee_id");
        }
    }

    public function getAttendanceLogInsAttribute()
    {
        $bufferInTimeEarly = Carbon::parse($this->startTime)->subHour((int)config("app.login_early"));
        // $bufferInTimeLate = Carbon::parse($this->startTime)->addHour((int)config("app.login_late"));
        return AttendanceLog::with(["department", "project"])
            ->where("log_type", AttendanceLogType::TIME_IN)
            ->whereTime('time', ">=", $bufferInTimeEarly)
            ->whereTime('time', "<=", $this->endTime)
            ->get();
    }
    public function getAttendanceLogOutsAttribute()
    {
        $bufferOutTimeEarly = $this->endTime->subHour((int)config("app.logout_early"));
        $bufferOutTimeLate = $this->endTime->addHour((int)config("app.logout_late"));
        return AttendanceLog::with(["department", "project"])
            ->where("log_type", AttendanceLogType::TIME_OUT)
            ->whereTime('time', ">=", $this->startTime)
            ->whereTime('time', "<=", $bufferOutTimeLate)
            ->get();
    }

    public function getDayOfWeekNamesAttribute()
    {
        $days = [
            'Sunday',
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday'
        ];
        return array_map(function ($day) use ($days) {
            return $days[$day];
        }, $this->daysOfWeek ?? []);
    }

    public function getDayOfWeekNamesShortAttribute()
    {
        $days = [
            'Sun',
            'Mon',
            'Tue',
            'Wed',
            'Thur',
            'Fri',
            'Sat'
        ];
        return array_map(function ($day) use ($days) {
            return $days[$day];
        }, $this->daysOfWeek ?? []);
    }

    public function getStartTimeHumanAttribute()
    {
        return Carbon::parse($this->startTime)->format("h:i A");
    }
    public function getEndTimeHumanAttribute()
    {
        return Carbon::parse($this->endTime)->format("h:i A");
    }
    /**
     * MODEL
     * LOCAL
     * SCOPES
     */

    public function scopeSchedulesOnDay(Builder $query, $date)
    {
        return $query->where(function ($query2) use ($date) {
            $query2->where('scheduleType', self::TYPE_REGULAR)
                ->where(function ($query3) use ($date) {
                    $query3->where(function ($query4) use ($date) {
                        $carbondate = new Carbon($date);
                        $query4->whereDate('startRecur', '<=', $date)
                            ->whereNotNull('endRecur')
                            ->whereDate('endRecur', '>', $date)
                            ->whereJsonContains("daysOfWeek", (string)$carbondate->dayOfWeek);
                    })
                        ->orWhere(function ($query5) use ($date) {
                            $carbondate = new Carbon($date);
                            $query5->whereDate('startRecur', '<=', $date)
                                ->whereNull('endRecur')
                                ->whereJsonContains("daysOfWeek", (string)$carbondate->dayOfWeek);
                        });
                });
        })->orWhere(function ($query6) use ($date) {
            $query6->where('scheduleType', self::TYPE_IRREGULAR)
                ->whereDate('startRecur', '=', $date);
        });
    }

    public function scopeRegularSchedules(Builder $query)
    {
        return $query->where('scheduleType', self::TYPE_REGULAR);
    }


    public function scopeIrregularSchedules(Builder $query)
    {
        return $query->where('scheduleType', self::TYPE_IRREGULAR);
    }

    public function scopeEmployeeSchedule(Builder $query, $date): Builder
    {
        return $query->where(function ($query) use ($date) {
            $query->whereDate('startRecur', '<=', $date)->whereNotNull('endRecur')->whereDate('endRecur', '>', $date);
        })->orWhere(function ($query) use ($date) {
            $query->whereDate('startRecur', '<=', $date)->whereNull('endRecur');
        });
    }

    public function scopePayrollSchedule(Builder $query, array $filters = [])
    {
        $query->whereBetween('startRecur', array($filters['cutoff_start'], $filters['cutoff_end']))
            ->where(function ($query) use ($filters) {
                if (array_key_exists('department_id', $filters)) {
                    return $query->where('department_id', $filters['department_id']);
                }
                return $query->where('project_id', $filters['project_id']);
            });
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

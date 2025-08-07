<?php

namespace App\Models;

use App\Enums\AttendanceLogType;
use App\Models\Traits\SeparatedCharging;
use App\Models\Traits\StatusScope;
use App\Traits\HasApproval;
use App\Traits\ModelHelpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;

class Overtime extends Model
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use HasApproval;
    use StatusScope;
    use SeparatedCharging;
    use ModelHelpers;

    protected $table = 'overtime';

    protected $fillable = [
        'id',
        'project_id',
        'department_id',
        'overtime_date',
        'overtime_start_time',
        'overtime_end_time',
        'reason',
        'meal_deduction',
        'approvals',
        'request_status',
        'created_by',
    ];

    protected $casts = [
        'approvals' => 'array',
        'overtime_start_time' => 'date:H:i',
        'overtime_end_time' => 'date:H:i',
        'overtime_date' => "datetime:Y-m-d",
    ];

    protected $appends = [
        'start_time_human',
        'end_time_human',
        'charging_class',
        'charging_id',
    ];

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, OvertimeEmployees::class);
    }

    public function department(): HasOne
    {
        return $this->hasOne(Department::class, "id", "department_id");
    }

    public function project(): HasOne
    {
        return $this->hasOne(Project::class, "id", "project_id");
    }
    public function charging(): HasOne
    {
        if ($this->department_id) {
            return $this->hasOne(Department::class, "id", "department_id");
        }
        if ($this->project_id) {
            return $this->hasOne(Project::class, "id", "project_id");
        }
        return $this->hasOne(Project::class, "id", "project_id");
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getCreatedByFullNameAttribute()
    {
        return Users::find($this->created_by)?->employee->fullname_last ?? 'USER NOT FOUND';
    }

    public function getDaysDelayedFilingAttribute()
    {
        $createdAt = Carbon::parse($this->created_at);
        $overtimeDate = Carbon::parse($this->overtime_date);
        return $createdAt->diffInDays($overtimeDate) > 0 ? $createdAt->diffInDays($overtimeDate) : 0;
    }

    public function scopeSetSection($query, $groupType, $id)
    {
        return $query->where($groupType, $id);
    }

    public function scopeBetweenDates(Builder $query, $dateFrom, $dateTo): void
    {
        $query->whereBetween('overtime_date', [$dateFrom, $dateTo]);
    }

    public function scopePayrollOvertime(Builder $query, array $filters = [])
    {
        $query->whereBetween('overtime_date', array($filters['cutoff_start'], $filters['cutoff_end']))
            ->where(function ($query) use ($filters) {
                if (array_key_exists('department_id', $filters)) {
                    return $query->where('department_id', $filters['department_id']);
                }
                return $query->where('project_id', $filters['project_id']);
            });
    }
    public function getTotalHourDurationAttribute()
    {
        return Carbon::parse($this->overtime_end_time)->diffInHours(Carbon::parse($this->overtime_start_time));
    }
    public function getStartTimeHumanAttribute()
    {
        return Carbon::parse($this->overtime_start_time)->format("h:i A");
    }

    public function getEndTimeHumanAttribute()
    {
        return Carbon::parse($this->overtime_end_time)->format("h:i A");
    }

    public function getBufferTimeStartEarlyAttribute()
    {
        $time = Carbon::parse($this->overtime_start_time);
        $newTime = $time->copy()->subHour((int)config("app.login_early"));
        if ($newTime->day !== $time->day) {
            $newTime = $time->copy()->startOfDay();
        }
        return $newTime->format("H:i:s");
        // return Carbon::parse($this->overtime_start_time)->subHour((int)config("app.login_early"));
    }
    public function getBufferTimeEndLateAttribute()
    {
        $time = Carbon::parse($this->overtime_end_time);
        $newTime = $time->copy()->addHour((int)config("app.logout_late"));
        if ($newTime->day !== $time->day) {
            $newTime = $time->copy()->endOfDay();
        }
        return $newTime->format("H:i:s");
        // return Carbon::parse($this->overtime_end_time)->addHour((int)config("app.logout_late"));
    }
    public function getAttendanceLogInsAttribute()
    {
        // login = (STARTTIME - BUFFER) to ENDTIME
        $bufferInTimeEarly = Carbon::parse($this->overtime_start_time)->subHour((int)config("app.login_early"));
        return AttendanceLog::with(["department", "project"])
            ->where("log_type", AttendanceLogType::TIME_IN)
            ->whereDate("date", "=", $this->overtime_date)
            ->whereTime('time', ">=", $bufferInTimeEarly)
            ->whereTime('time', "<=", $this->overtime_end_time)
            ->get();
    }

    public function getAttendanceLogOutsAttribute()
    {
        // Logout = STARTTIME to (ENDTIME + BUFFER)
        // $bufferOutTimeEarly = $this->overtime_start_time;
        $bufferOutTimeLate = $this->overtime_end_time->addUnitNoOverflow("hour", (int)config("app.logout_late"), "day");
        return AttendanceLog::with(["department", "project"])
            ->where("log_type", AttendanceLogType::TIME_OUT)
            ->whereDate("date", "=", $this->overtime_date)
            ->whereTime('time', ">=", $this->overtime_start_time)
            ->whereTime('time', "<=", $bufferOutTimeLate)
            ->get();
    }

    public function getOvertimeDateHumanAttribute()
    {
        return $this->overtime_date ? Carbon::parse($this->overtime_date)->format("F j, Y") : null;
    }

    public function getSectionNameAttribute()
    {
        if ($this->project_id) {
            return $this->project->project_code;
        }
        if ($this->department_id) {
            return $this->department->department_name;
        }
        return 'No Section found.';
    }
}

<?php

namespace App\Models;

use App\Enums\AttendanceLogType;
use App\Enums\PersonelAccessForm;
use App\Models\Traits\StatusScope;
use App\Traits\HasApproval;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
    use HasApproval, StatusScope;


    protected $table = 'overtime';

    protected $fillable = [
        'id',
        'project_id',
        'department_id',
        'overtime_date',
        'overtime_start_time',
        'overtime_end_time',
        'reason',
        'prepared_by',
        'approvals',
        'request_status',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function scopeRequestStatusPending(Builder $query): void
    {
        $query->where('request_status', PersonelAccessForm::REQUESTSTATUS_PENDING);
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

    public function getStartTimeHumanAttribute()
    {
        return Carbon::parse($this->overtime_start_time)->format("h:i A");
    }

    public function getEndTimeHumanAttribute()
    {
        return Carbon::parse($this->overtime_end_time)->format("h:i A");
    }

    function getChargingNameAttribute() {
        if($this->project_id){
            return $this->project->project_code;
        }
        if($this->department_id){
            return $this->department->department_name;
        }
        return 'No charging found.';
    }

    public function getAttendanceLogInsAttribute()
    {
        $bufferInTimeEarly = Carbon::parse($this->overtime_start_time)->subHour((int)config("app.login_early"));
        $bufferInTimeLate = Carbon::parse($this->overtime_start_time)->addHour((int)config("app.login_late"));
        return AttendanceLog::with(["department", "project"])
            ->where("log_type", AttendanceLogType::TIME_IN)
            ->whereDate("date", "=", $this->overtime_date)
            ->whereTime('time', ">=", $bufferInTimeEarly)
            ->whereTime('time', "<=", $bufferInTimeLate)
            ->get();
    }

    public function getAttendanceLogOutsAttribute()
    {
        $bufferOutTimeEarly = $this->overtime_start_time;
        $bufferOutTimeLate = $this->overtime_end_time->addUnitNoOverflow("hour", (int)config("app.logout_late"), "day");
        return AttendanceLog::with(["department", "project"])
            ->where("log_type", AttendanceLogType::TIME_OUT)
            ->whereDate("date", "=", $this->overtime_date)
            ->whereTime('time', ">=", $bufferOutTimeEarly)
            ->whereTime('time', "<=", $bufferOutTimeLate)
            ->get();
    }

}

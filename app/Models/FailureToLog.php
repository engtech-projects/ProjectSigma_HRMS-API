<?php

namespace App\Models;

use App\Enums\AssignTypes;
use App\Traits\HasApproval;
use App\Enums\AttendanceLogType;
use App\Enums\AttendanceType;
use App\Enums\RequestStatuses;
use App\Models\Traits\HasEmployee;
use App\Traits\ModelHelpers;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FailureToLog extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasEmployee;
    use HasApproval;
    use ModelHelpers;

    public const DEPARTMENT = "App\Models\Department";
    public const PROJECT = "App\Models\Project";

    protected $fillable = [
        'date',
        'time',
        'log_type',
        'reason',
        'employee_id',
        'charging_type',
        'charging_id',
        'approvals',
        'request_status',
        'created_by',
    ];
    protected $casts = [
        'date' => 'date:Y-m-d',
        'time' => 'date:H:i:s',
        'log_type' => AttendanceLogType::class,
        'approvals' => 'array',
        'employee_id' => 'integer',
    ];

    protected $appends = [
        'charging_designation',
    ];

    public function charging(): MorphTo
    {
        return $this->morphTo();
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->created_by = auth()->user()->id;
        });
    }

    public function completeRequestStatus()
    {
        $this->request_status = RequestStatuses::APPROVED->value;
        AttendanceLog::create([
            'date' => $this->date,
            'time' => $this->time,
            'log_type' => $this->log_type,
            'attendance_type' => AttendanceType::MANUAL, // SHOULD BE FAILURE TO LOG
            'department_id' => $this->charging_department_id,
            'project_id' => $this->charging_project_id,
            'employee_id' => $this->employee_id,
        ]);
        $this->save();
        $this->refresh();
    }


    public function requestStatusCompleted(): bool
    {
        if ($this->request_status == RequestStatuses::APPROVED->value) {
            return true;
        }
        return false;
    }

    public function scopeBetweenDates(Builder $query, $dateFrom, $dateTo): void
    {
        $query->whereBetween('date', [$dateFrom, $dateTo]);
    }

    public function getTimeHumanAttribute()
    {
        return Carbon::parse($this->time)->format("h:i A");
    }

    public function getDateHumanAttribute()
    {
        return Carbon::parse($this->date)->format("F j, Y");
    }

    public function getDaysDelayedFilingAttribute()
    {
        $createdAt = Carbon::parse($this->created_at);
        $date = Carbon::parse($this->date);
        return $createdAt->diffInDays($date) > 0 ? $createdAt->diffInDays($date) : 0;
    }

    public function getChargingProjectIdAttribute()
    {
        return ($this->charging_type === Project::class || $this->charging_type === AssignTypes::PROJECT->value) ? $this->charging_id : null;
    }
    public function getChargingDepartmentIdAttribute()
    {
        return ($this->charging_type === Department::class || $this->charging_type === AssignTypes::DEPARTMENT->value) ? $this->charging_id : null;
    }
    public function getChargingDesignationAttribute()
    {
        if ($this->charging_department_id) {
            return Department::find($this->charging_department_id)?->department_name;
        }
        if ($this->charging_project_id) {
            return Project::find($this->charging_project_id)?->project_code;
        }
        return "No charging found.";
    }
}

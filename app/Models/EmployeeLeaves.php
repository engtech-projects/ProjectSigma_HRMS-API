<?php

namespace App\Models;

use App\Enums\PersonelAccessForm;
use App\Models\Traits\SeparatedCharging;
use App\Models\Traits\StatusScope;
use App\Traits\HasApproval;
use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;

class EmployeeLeaves extends Model
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use HasApproval;
    use HasUser;
    use StatusScope;
    use SeparatedCharging;

    protected $casts = [
        "approvals" => "array",
        "created_at" => "date:Y-m-d",
        "date_of_absence_from" => "date:Y-m-d",
        "date_of_absence_to" => "date:Y-m-d"
    ];

    protected $fillable = [
        'id',
        'employee_id',
        'department_id',
        'project_id',
        'leave_id',
        'other_absence',
        'date_of_absence_from',
        'date_of_absence_to',
        'reason_for_absence',
        'approvals',
        'request_status',
        'number_of_days',
        'with_pay',
        'created_by',
    ];

    public function scopeRequestStatusPending(Builder $query): void
    {
        $query->where('request_status', PersonelAccessForm::REQUESTSTATUS_PENDING);
    }

    public function denyRequestStatus()
    {
        $this->request_status = PersonelAccessForm::REQUESTSTATUS_DISAPPROVED;
        $this->save();
        $this->refresh();
    }

    public function scopeApproval($query)
    {
        return $query->where("request_status", "=", "Pending");
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, "id", "employee_id");
    }

    public function department(): HasOne
    {
        return $this->hasOne(Department::class, "id", "department_id");
    }

    public function project(): HasOne
    {
        return $this->hasOne(Project::class, "id", "project_id");
    }
    public function charging()
    {
        if ($this->department_id) {
            return $this->department;
        }
        if ($this->project_id) {
            return $this->project;
        }
    }

    public function leave(): BelongsTo
    {
        return $this->belongsTo(Leave::class, "leave_id", "id");
    }

    public function scopeWithPayLeave(Builder $query): void
    {
        $query->where('with_pay', true);
    }
    public function scopePayrollLeave(Builder $query, array $filters = [])
    {
        $query->whereBetween('date_of_absence_from', array($filters['cutoff_start'], $filters['cutoff_end']))
            ->where(function ($query) use ($filters) {
                if (array_key_exists('department_id', $filters)) {
                    return $query->where('department_id', $filters['department_id']);
                }
                return $query->where('project_id', $filters['project_id']);
            });
    }
    public function durationForDate($date)
    {
        if ($this->number_of_days < 1 || $this->date_of_absence_to == $date) {
            return $this->number_of_days % 1 != 0 ? 0.5 : 1;
        }
        if ($date->lt($this->date_of_absence_from) || $date->gt($this->date_of_absence_to)) {
            return 0;
        }
        return 1;
    }
    public function scopeBetweenDates(Builder $query, $dateFrom, $dateTo)
    {
        return $query->whereBetween('date_of_absence_from', [$dateFrom, $dateTo])
        ->orwhereBetween('date_of_absence_to', [$dateFrom, $dateTo])
        ->orWhere(function($query) use ($dateFrom, $dateTo) {
            $query->where('date_of_absence_from', '<=', $dateFrom)
                  ->where('date_of_absence_to', '>=', $dateTo);
        });
    }
}

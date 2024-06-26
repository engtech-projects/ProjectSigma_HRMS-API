<?php

namespace App\Models;

use App\Enums\PersonelAccessForm;
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

    public function department(): HasOne
    {
        return $this->hasOne(Department::class, "id", "department_id");
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, "id", "employee_id");
    }

    public function project(): HasOne
    {
        return $this->hasOne(Project::class, "id", "project_id");
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
}

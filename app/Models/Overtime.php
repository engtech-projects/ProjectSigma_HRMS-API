<?php

namespace App\Models;

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
        'employee_id',
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
        'overtime_start_time' => 'date:H:i:s',
        'overtime_end_time' => 'date:H:i:s',
        'overtime_date' => "datetime:Y-m-d",
    ];

    protected $appends = [
        'start_time_human',
        'end_time_human',
    ];

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, "id", "employee_id");
    }

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

    public function overtimeEmployees(): HasMany
    {
        return $this->hasMany(overtimeEmployees::class, 'overtime_id', 'id');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'released_by');
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
}

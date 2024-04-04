<?php

namespace App\Models;

use App\Enums\PersonelAccessForm;
use App\Traits\HasApproval;
use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'type',
        'other_absence',
        'date_of_absence_from',
        'date_of_absence_to',
        'reason_for_absence',
        'approvals',
        'request_status',
        'number_of_days',
        'with_pay',
    ];

    public function scopeRequestStatusPending(Builder $query): void
    {
        $query->where('request_status', PersonelAccessForm::REQUESTSTATUS_PENDING);
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
}

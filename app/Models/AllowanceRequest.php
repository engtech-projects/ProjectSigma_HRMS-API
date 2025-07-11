<?php

namespace App\Models;

use App\Traits\HasApproval;
use App\Traits\ModelHelpers;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AllowanceRequest extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasApproval;
    use ModelHelpers;

    public const DEPARTMENT = "App\Models\Department";
    public const PROJECT = "App\Models\Project";

    protected $table = 'allowance_request';

    protected $casts = [
        "deduction_date_start" => "date:Y-m-d",
        "cutoff_start" => "date:Y-m-d",
        "cutoff_end" => "date:Y-m-d",
        'approvals' => 'array',
    ];

    protected $fillable = [
        'id',
        'charge_assignment_id',
        'charge_assignment_type',
        'allowance_date',
        'allowance_amount',
        'cutoff_start',
        'cutoff_end',
        'total_days',
        'approvals',
        'request_status',
        'created_by',
    ];

    protected $appends = ['charging_name'];

    public function charge_assignment(): MorphTo
    {
        return $this->morphTo();
    }

    public function employee_allowances(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, EmployeeAllowances::class, "allowance_request_id", "employee_id")
        ->withPivot(["allowance_amount", "allowance_rate", "allowance_days"])
        ->withTimestamps();
    }

    public function getCutoffStartHumanAttribute()
    {
        return Carbon::parse($this->cutoff_start)->format("F j, Y");
    }

    public function getCutoffEndHumanAttribute()
    {
        return Carbon::parse($this->cutoff_end)->format("F j, Y");
    }

    public function getAllowanceDateHumanAttribute()
    {
        return Carbon::parse($this->allowance_date)->format("F j, Y");
    }

    public function scopeBetweenDates($query, $dateFrom, $dateTo)
    {
        $query->whereBetween('allowance_date', [$dateFrom, $dateTo]);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, "charge_assignment_id");
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, "charge_assignment_id");
    }

    public function getChargingNameAttribute()
    {
        if ($this->charge_assignment_type === AllowanceRequest::PROJECT) {
            return $this->project->project_code;
        }
        if ($this->charge_assignment_type === AllowanceRequest::DEPARTMENT) {
            return $this->department->department_name;
        }
        return 'No charging found.';
    }

    public function getProjectIdentifierNameAttribute()
    {
        if ($this->charge_assignment_type === AllowanceRequest::PROJECT) {
            return $this->project->employeeInternalWorks->first()?->work_location ? $this->project->employeeInternalWorks->first()?->work_location : 'No work location found.';
        }
        if ($this->charge_assignment_type === AllowanceRequest::DEPARTMENT) {
            return "Office";
        }
        return 'No work location found.';
    }
}

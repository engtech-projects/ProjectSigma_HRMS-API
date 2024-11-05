<?php

namespace App\Models;

use App\Enums\PersonelAccessForm;
use App\Traits\HasApproval;
use App\Traits\ModelHelpers;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
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
        'created_by',
    ];

    public function charge_assignment(): MorphTo
    {
        return $this->morphTo();
    }

    public function employee_allowances(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, EmployeeAllowances::class, "allowance_request_id", "employee_id")->withPivot(["allowance_amount", "allowance_rate", "allowance_days"]);
    }

    public function scopeRequestStatusPending(Builder $query): void
    {
        $query->where('request_status', PersonelAccessForm::REQUESTSTATUS_PENDING);
    }

    public function scopeRequestStatusApproved(Builder $query): void
    {
        $query->where('request_status', PersonelAccessForm::REQUESTSTATUS_APPROVED);
    }

    public function created_by_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
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
}

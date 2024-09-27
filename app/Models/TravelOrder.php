<?php

namespace App\Models;

use App\Enums\AssignTypes;
use App\Enums\PersonelAccessForm;
use App\Traits\HasApproval;
use App\Traits\HasUser;
use Carbon\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class TravelOrder extends Model
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use HasApproval;
    use HasUser;

    protected $casts = [
        "approvals" => "array",
        "date_of_travel" => "date:Y-m-d",
        "time_of_travel" => "date:H:i",
    ];

    protected $fillable = [
        'id',
        'requesting_office',
        'destination',
        'purpose_of_travel',
        'date_of_travel',
        'time_of_travel',
        'duration_of_travel',
        'means_of_transportation',
        'remarks',
        'created_by',
        'approvals',
        'request_status',
        'charge_type',
        'charge_id',
    ];

    protected $appends = [
        'charging_designation',
    ];

    public function scopeRequestStatusPending(Builder $query): void
    {
        $query->where('request_status', PersonelAccessForm::REQUESTSTATUS_PENDING);
    }

    public function scopeRequestStatusApproved(Builder $query): void
    {
        $query->where('request_status', PersonelAccessForm::REQUESTSTATUS_APPROVED);
    }

    public function scopeApproval($query)
    {
        return $query->where("request_status", "=", "Pending");
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, TravelOrderMembers::class);
    }

    public function department(): HasOne
    {
        return $this->hasOne(Department::class, "id", "requesting_office");
    }

    public function charge(): MorphTo
    {
        return $this->morphTo();
    }

    public function travelOrders(): HasMany
    {
        return $this->hasMany(TravelOrderMembers::class, "id", "travel_order_id");
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTimeOfTravelHumanAttribute()
    {
        return Carbon::parse($this->time_of_travel)->format("h:i A");
    }
    public function getDateOfTravelHumanAttribute()
    {
        return Carbon::parse($this->date_of_travel)->format("F j, Y");
    }
    public function getDateTimeStartAttribute()
    {
        return $this->date_of_travel
            ->setHour($this->time_of_travel->get("hour"))
            ->setMinute($this->time_of_travel->get("minute"))->format("Y-m-d H:i:s");
    }
    public function getDateTimeEndAttribute()
    {
        return Carbon::parse($this->date_time_start)
            ->addHour($this->duration_of_travel * 24)->format("Y-m-d H:i:s");
    }
    public function datetimeIsApplicable($datetime)
    {
        $dt = Carbon::parse($datetime);
        return $dt->gte($this->date_time_start) && $dt->lte($this->date_time_end);
    }
    public function getChargingDepartmentIdAttribute()
    {
        return ($this->charge_type === Department::class || $this->charge_type === AssignTypes::DEPARTMENT->value) ? $this->charge_id : null;
    }
    public function getChargingProjectIdAttribute()
    {
        return ($this->charge_type === Department::class || $this->charge_type === AssignTypes::PROJECT->value) ? $this->charge_id : null;
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

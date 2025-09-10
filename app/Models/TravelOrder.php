<?php

namespace App\Models;

use App\Enums\AssignTypes;
use App\Traits\HasApproval;
use App\Traits\HasUser;
use App\Traits\ModelHelpers;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class TravelOrder extends Model
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use HasApproval;
    use HasUser;
    use ModelHelpers;

    public const DEPARTMENT = \App\Models\Department::class;
    public const PROJECT = \App\Models\Project::class;

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
        'charge_type',
        'charge_id',
        'approvals',
        'request_status',
        'created_by',
    ];

    protected $appends = [
        'charging_designation',
        "date_time_start",
        "date_time_end",
    ];

    public function scopeBetweenDates($query, $dateFrom, $dateTo)
    {
        return $query->whereBetween('date_of_travel', [$dateFrom, $dateTo])
        ->orWhere(function ($query) use ($dateFrom, $dateTo) {
            $query->whereRaw('DATE_ADD(date_of_travel, INTERVAL duration_of_travel DAY) BETWEEN ? AND ?', [$dateFrom, $dateTo]);
        });
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, TravelOrderMembers::class)
        ->whereNull("travel_order_members.deleted_at");
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
    public function getDateTimeEndHumanAttribute()
    {
        return Carbon::parse($this->date_time_start)
            ->addHour($this->duration_of_travel * 24)->format("F j, Y h:i A");
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
        return ($this->charge_type === Project::class || $this->charge_type === AssignTypes::PROJECT->value) ? $this->charge_id : null;
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

    public function getDaysDelayedFilingAttribute()
    {
        $createdAt = Carbon::parse($this->created_at);
        $travelDate = Carbon::parse($this->date_of_travel);
        return $createdAt->diffInDays($travelDate) > 0 ? $createdAt->diffInDays($travelDate) : 0;
    }
}

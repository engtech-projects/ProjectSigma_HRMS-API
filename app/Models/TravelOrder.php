<?php

namespace App\Models;

use App\Enums\PersonelAccessForm;
use App\Traits\HasApproval;
use App\Traits\HasUser;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
        "date_and_time_of_travel" => "date:Y-m-d",
        "date_of_absence_to" => "date:Y-m-d"
    ];

    protected $fillable = [
        'id',
        'name',
        'requesting_office',
        'destination',
        'purpose_of_travel',
        'date_and_time_of_travel',
        'duration_of_travel',
        'means_of_transportation',
        'remarks',
        'requested_by',
        'approvals',
        'request_status',
    ];

    public function scopeRequestStatusPending(Builder $query): void
    {
        $query->where('request_status', PersonelAccessForm::REQUESTSTATUS_PENDING);
    }

    public function scopeApproval($query)
    {
        return $query->where("request_status", "=", "Pending");
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class);
    }

    public function department(): HasOne
    {
        return $this->hasOne(Department::class, "id", "requesting_office");
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
}

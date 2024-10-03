<?php

namespace App\Models;

use App\Enums\PostingStatusType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class OtherDeductionPayments extends Model
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use HasApiTokens;

    protected $fillable = [
        'id',
        'otherdeduction_id',
        'amount_paid',
        'date_paid',
        'posting_status',
        'payment_type',
    ];

    public function otherdeduction(): BelongsTo
    {
        return $this->belongsTo(OtherDeduction::class);
    }

    public function employee(): HasOneThrough
    {
        return $this->hasOneThrough(Employee::class, OtherDeduction::class, "id", "id", "otherdeduction_id", "employee_id");
    }

    public function getDatePaidHumanAttribute()
    {
        return Carbon::parse($this->date_paid)->format("F j, Y");
    }

    public function scopeIsPosted($query)
    {
        return $query->where('posting_status', PostingStatusType::POSTED->value);
    }

    public function scopeIsNotPosted($query)
    {
        return $query->where('posting_status', PostingStatusType::NOTPOSTED->value);
    }

}

<?php

namespace App\Models;

use App\Enums\PostingStatusType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class LoanPayments extends Model
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    protected $casts = [
        "date_paid" => "date:Y-m-d",
    ];

    protected $fillable = [
        'id',
        'loans_id',
        'amount_paid',
        'date_paid',
        'payment_type',
        'posting_status',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loans::class, 'loans_id', 'id');
    }

    public function employee(): HasOneThrough
    {
        return $this->hasOneThrough(Employee::class, Loans::class, "id", "id", "loans_id", "employee_id");
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

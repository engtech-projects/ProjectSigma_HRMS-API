<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class CashAdvancePayments extends Model
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'id',
        'cashadvance_id',
        'amount_paid',
        'date_paid',
        'payment_type',
        'posting_status',
    ];

    public function cashadvance(): BelongsTo
    {
        return $this->belongsTo(CashAdvance::class);
    }

    public function employee(): HasOneThrough
    {
        return $this->hasOneThrough(Employee::class, CashAdvance::class, "id", "id", "cashadvance_id", "employee_id");
    }

}

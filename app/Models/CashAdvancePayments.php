<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class CashAdvancePayments extends Model
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'id',
        'cashadvance_id',
        'amount_paid',
        'date_paid',
        'payment_type',
        'posting_status',
    ];

    public function cashadvance(): HasOne
    {
        return $this->hasOne(CashAdvance::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class LoanPayments extends Model
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

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

    public function loan(): HasOne
    {
        return $this->hasOne(Loans::class);
    }
}

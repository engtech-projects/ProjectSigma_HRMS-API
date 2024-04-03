<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
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

    public function otherdeduction(): HasOne
    {
        return $this->hasOne(OtherDeduction::class);
    }
}

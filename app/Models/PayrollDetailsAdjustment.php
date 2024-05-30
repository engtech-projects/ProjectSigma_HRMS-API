<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PayrollDetailsAdjustment extends Model
{

    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use HasApiTokens;

    protected $fillable = [
        'id',
        'name',
        'amount',
        'payroll_details_id',
    ];

    public function payroll_details(): BelongsTo
    {
        return $this->belongsTo(payrollDetails::class);
    }

}

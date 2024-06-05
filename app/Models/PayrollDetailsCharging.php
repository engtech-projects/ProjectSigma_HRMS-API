<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PayrollDetailsCharging extends Model
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use HasApiTokens;

    protected $table = "payroll_details_charging";

    protected $fillable = [
        'id',
        'name',
        'amount',
        'payroll_details_id',
        'charge_type',
        'charge_id',
    ];

    public function payroll_details(): BelongsTo
    {
        return $this->belongsTo(payrollDetails::class);
    }

    public function charge(): MorphTo
    {
        return $this->morphTo();
    }
}

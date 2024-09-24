<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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

    protected $appends = [
        "charging_name",
    ];

    public function payroll_details(): BelongsTo
    {
        return $this->belongsTo(PayrollDetail::class);
    }

    public function charge(): MorphTo
    {
        return $this->morphTo();
    }

    public function getChargingNameAttribute()
    {
        if($this->charge_type == AttendancePortal::DEPARTMENT) {
            return $this->charge->department_name;
        }
        if($this->charge_type == AttendancePortal::PROJECT) {
            return $this->charge->project_code;
        }
        return 'No charging found.';
    }

}

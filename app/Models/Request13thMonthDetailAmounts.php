<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Request13thMonthDetailAmounts extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'request_13th_month_detail_amt';

    protected $fillable = [
        // Add fillable fields based on migration
        'request_13th_month_detail_id',
        'charge_type', // Assuming this is the morph type
        'charge_id',   // Assuming this is the morph ID
        'total_payroll',
        'amount',
        'metadata',
    ];

    protected $casts = [
        'total_payroll' => 'decimal:2',
        'amount' => 'decimal:2',
        'metadata' => 'array',
    ];

    /**
    * ==================================================
    * MODEL RELATIONSHIPS
    * ==================================================
    */
    public function detail()
    {
        return $this->belongsTo(Request13thMonthDetails::class, 'request_13th_month_detail_id', 'id');
    }

    public function chargeable()
    {
        return $this->morphTo();
    }
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, "charge_id");
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, "charge_id");
    }
    /**
    * ==================================================
    * MODEL ATTRIBUTES
    * ==================================================
    */
    public function getChargingNameAttribute()
    {
        if ($this->charge_type == AttendancePortal::DEPARTMENT) {
            return $this->department?->department_name ?? "";
        }
        if ($this->charge_type == AttendancePortal::PROJECT) {
            return $this->project?->project_code ?? "";
        }
        return 'No charging found.';
    }
    /**
    * ==================================================
    * STATIC SCOPES
    * ==================================================
    */
    /**
    * ==================================================
    * DYNAMIC SCOPES
    * ==================================================
    */
    /**
    * ==================================================
    * MODEL FUNCTIONS
    * ==================================================
    */

}

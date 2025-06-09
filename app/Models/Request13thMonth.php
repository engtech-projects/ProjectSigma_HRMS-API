<?php

namespace App\Models;

use App\Traits\HasApproval;
use App\Traits\ModelHelpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Request13thMonth extends Model
{
    use HasFactory;
    use HasApproval;
    use ModelHelpers;
    use SoftDeletes;

    protected $table = 'request_13th_months';

    protected $fillable = [
        "date_requested",
        "date_from",
        "date_to",
        "employees",
        "days_advance",
        "charging_type",
        "charging_id",
        "metadata",
        "approvals",
        "request_status",
        "created_by",
    ];

    protected $casts = [
        'date_requested' => 'date:Y-m-d',
        'date_from' => 'date:Y-m-d',
        'date_to' => 'date:Y-m-d',
        'employees' => 'array',
        'metadata' => 'array',
        'approvals' => 'array',
    ];

    /**
    * ==================================================
    * MODEL RELATIONSHIPS
    * ==================================================
    */
    public function details()
    {
        return $this->hasMany(Request13thMonthDetails::class, 'request_13th_months_id', "id");
    }

    public function charging()
    {
        return $this->morphTo();
    }
    /**
    * ==================================================
    * MODEL ATTRIBUTES
    * ==================================================
    */
    public function getDateRequestedHumanAttribute()
    {
        return $this->date_requested->format('F j, Y');
    }
    public function getDateFromHumanAttribute()
    {
        return $this->date_from?->format('F j, Y');
    }
    public function getDateToHumanAttribute()
    {
        return $this->date_to?->format('F j, Y');
    }
    public function getPayrollDurationHumanAttribute()
    {
        return $this->date_from_human . ' - ' . $this->date_to_human;
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Request13thMonthDetails extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'request_13th_month_details';
    protected $fillable = [
        'request_13th_months_id',
        'employee_id',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * ==================================================
     * MODEL RELATIONSHIPS
     * ==================================================
     */
    public function request13thMonth()
    {
        return $this->belongsTo(Request13thMonth::class, 'request_13th_months_id');
    }

    public function amounts()
    {
        return $this->hasMany(Request13thMonthDetailAmounts::class, 'request_13th_month_detail_id', 'id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    /**
    * ==================================================
    * MODEL ATTRIBUTES
    * ==================================================
    */
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

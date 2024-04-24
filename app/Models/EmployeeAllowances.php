<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeAllowances extends Model
{
    use HasFactory, SoftDeletes;

    protected $appends = ['total_amount'];

    public function getTotalAmountAttribute()
    {
        return $this->allowance_amount * $this->total_days;
    }

    protected $casts = [
        "deduction_date_start" => "date:Y-m-d",
        "cutoff_start" => "date:Y-m-d",
        "cutoff_end" => "date:Y-m-d",
    ];

    protected $fillable = [
        'id',
        'charge_assignment_id',
        'charge_assignment_type',
        'allowance_date',
        'allowance_amount',
        'cutoff_start',
        'cutoff_end',
        'total_days',
    ];

    public function charge_assignment(): MorphTo
    {
        return $this->morphTo();
    }
}

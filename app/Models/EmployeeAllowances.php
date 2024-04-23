<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeAllowances extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        "deduction_date_start" => "date:Y-m-d",
    ];

    protected $fillable = [
        'id',
        'charge_assignment_id',
        'charge_assignment_type',
        'allowance_date',
        'allowance_amount',
    ];

    public function charge_assignment()
    {
        return $this->morphTo();
    }
}

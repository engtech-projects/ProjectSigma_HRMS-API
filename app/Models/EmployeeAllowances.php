<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasApproval;

class EmployeeAllowances extends Model
{
    use HasFactory, SoftDeletes, HasApproval;

    protected $appends = ['total_amount'];

    public function getTotalAmountAttribute()
    {
        return $this->allowance_amount * $this->allowance_days;
    }

    protected $fillable = [
        'id',
        'allowance_amount',
        'allowance_request_id',
        'employee_id',
        'allowance_rate',
        'allowance_days',
        'created_by',
    ];
}

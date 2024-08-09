<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeAllowances extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $appends = ['total_amount'];

    public $timestamps = true;

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

    public function allowance_request(): BelongsTo
    {
        return $this->belongsTo(AllowanceRequest::class, "allowance_request_id");
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, "employee_id");
    }
}

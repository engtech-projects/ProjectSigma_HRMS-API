<?php

namespace App\Models;

use App\Traits\HasApproval;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Leave extends Model
{
    use SoftDeletes;
    use HasApproval;

    protected $table = "employee_leaves";

    protected $fillable = [
        'id',
        'leave_name',
        'amt_of_leave',
        'employment_type',
    ];

    protected $casts = [
        'approvals' => 'array'
    ];

    /**
     * MODEL
     * LOCAL
     * SCOPES
     */

    public function scopeWithPayLeave(Builder $query): void
    {
        $query->where('with_pay', true);
    }
}

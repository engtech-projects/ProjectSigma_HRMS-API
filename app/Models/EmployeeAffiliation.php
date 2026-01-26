<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeAffiliation extends Model
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    protected $fillable = [
        'id',
        'employee_id',
        'club_organization_name',
        'membership_type',
        'status',
        'membership_exp_date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}

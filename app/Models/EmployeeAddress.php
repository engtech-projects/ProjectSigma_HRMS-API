<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeAddress extends Model
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    protected $fillable = [
        'id',
        'employee_id',
        'street',
        'brgy',
        'city',
        'zip',
        'province',
        'type',
    ];
    protected $appends = [
        "complete_address"
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getCompleteAddressAttribute()
    {
        return $this->street . ", " . $this->brgy . ", " . $this->city . ", " . $this->province;
    }
}

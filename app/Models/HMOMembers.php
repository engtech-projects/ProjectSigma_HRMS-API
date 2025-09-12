<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class HMOMembers extends Model
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    protected $table = 'hmo_members';

    public $rules = [
        'employee_id' => 'required|member_type:employee',
    ];

    protected $fillable = [
        'id',
        'hmo_id',
        'member_type',
        'employee_id',
        'member_name',
        'member_belongs_to',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function hmo(): BelongsTo
    {
        return $this->belongsTo(HMO::class);
    }
}

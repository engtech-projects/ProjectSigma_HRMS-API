<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Position extends Model
{
    use HasApiTokens, HasFactory, Notifiable,SoftDeletes;

    protected $table = 'positions';

    protected $fillable = [
        'id',
        'name',
        'department_id',
        'position_type',
    ];

    public function allowances(): HasOne
    {
        return $this->hasOne(Allowance::class);
    }

    public function departments(): HasOne
    {
        return $this->hasOne(Position::class,'department_id');
    }

    public function departmentsdata(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}

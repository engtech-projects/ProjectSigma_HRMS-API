<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class HMO extends Model
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'hmo';

    protected $fillable = [
        'id',
        'hmo_name',
        'hmo_start',
        'hmo_end',
        'employee_share',
        'employer_share',
    ];

    public function hmoMembers(): HasMany
    {
        return $this->hasMany(HMOMembers::class, "hmo_id");
    }
}

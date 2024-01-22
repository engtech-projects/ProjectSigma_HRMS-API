<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
    use HasApiTokens, HasFactory, Notifiable,SoftDeletes;
    
    protected $table = 'positions';

    protected $fillable = [
        'id',
        'name',
    ];   

    public function allowances(): HasMany
    {
        return $this->hasMany(allowance::class,"id","position_id");
    }
}
    
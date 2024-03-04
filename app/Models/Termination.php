<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Termination extends Model
{
    use HasApiTokens, HasFactory, Notifiable,SoftDeletes;
    protected $fillable = [
        'id',
        'employee_id',
        'type_of_termination',
        'reason_for_termination',
        'eligible_for_rehire',
        'last_day_worked',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class UserAccessibilities extends Model
{
    use HasApiTokens, HasFactory, Notifiable,SoftDeletes;
    
    protected $fillable = [
        'id',
        'user_id',
        'options',
        'updated_at',
        'created_at',
    ];
}

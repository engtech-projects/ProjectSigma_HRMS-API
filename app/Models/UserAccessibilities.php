<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\softDeletes;
use Laravel\Sanctum\HasApiTokens;

class UserAccessibilities extends Model
{
    use HasApiTokens, HasFactory, Notifiable,softDeletes;
    
    protected $fillable = [
        'id',
        'user_id',
        'options',
        'updated_at',
        'created_at',
    ];
}

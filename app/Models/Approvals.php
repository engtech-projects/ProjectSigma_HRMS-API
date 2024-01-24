<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Approvals extends Model
{
    use HasApiTokens, HasFactory, Notifiable,SoftDeletes;

    protected $fillable = [
        'id',
        'form',
        'approvals',
    ];    
}

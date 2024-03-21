<?php

namespace App\Models;

use App\Traits\HasApproval;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Approvals extends Model
{
    use HasApiTokens, HasFactory, Notifiable,SoftDeletes, HasApproval;

    protected $fillable = [
        'id',
        'form',
        'approvals',
    ];

    protected $casts = [
        "approval" => "array"
    ];
}

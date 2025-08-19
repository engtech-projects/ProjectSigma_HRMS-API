<?php

namespace App\Models;

use App\Traits\HasApproval;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Approvals extends Model
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use HasApproval;

    protected $fillable = [
        'id',
        'form',
        'module',
        'approvals',
    ];

    protected $casts = [
        "approvals" => "array"
    ];
}

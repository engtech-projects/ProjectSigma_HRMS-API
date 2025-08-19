<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class TravelOrderMembers extends Model
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    protected $fillable = [
        'id',
        'travel_order_id',
        'employee_id',
    ];
}

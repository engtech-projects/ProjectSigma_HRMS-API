<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class TravelOrder extends Model
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;
    protected $fillable = [
        'id',
        'name',
        'requesting_office',
        'destination',
        'purpose_of_travel',
        'date_and_time_of_travel',
        'duration_of_travel',
        'means_of_transportation',
        'remarks',
        'requested_by',
        'approvals',
    ];
}

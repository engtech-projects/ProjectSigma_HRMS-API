<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class ExternalWorkExperience extends Model
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'id',
        'employee_id',
        'position_title',
        'company_name',
        'salary',
        'status_of_appointment',
        'date_from',
        'date_to',
    ];

    protected $casts = [
        "date_from" => "date:Y-m-d",
        "date_to" => "date:Y-m-d",
    ];
}

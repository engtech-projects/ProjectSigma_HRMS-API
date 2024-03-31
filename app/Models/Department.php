<?php

namespace App\Models;

use App\Models\Traits\HasAttendanceLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Department extends Model
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;
    use HasAttendanceLog;

    protected $fillable = [
        'id',
        'department_name',
        'updated_at',
        'created_at',
    ];


    /**
     * MODEL
     * RELATED
     * RELATION
     */
}

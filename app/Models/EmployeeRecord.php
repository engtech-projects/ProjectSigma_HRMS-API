<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class EmployeeRecord extends Model
{
    use HasApiTokens, HasFactory, Notifiable,SoftDeletes;

    protected $table = 'employment_records';

    protected $fillable = [
        'id',
        'employee_id',
        'employment_status',
        'position',
        'department',
        'division',
        'section_program',
    ];
}

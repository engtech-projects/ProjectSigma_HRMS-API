<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Leave extends Model
{
    use SoftDeletes;

    protected $table = "leaves";

    protected $fillable = [
        'id',
        'leave_name',
        'amt_of_leave',
        'employment_status',
    ];

    protected $casts = [
        'employment_status' => 'array'
    ];

    /**
     * MODEL
     * LOCAL
     * SCOPES
     */
}

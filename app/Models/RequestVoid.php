<?php

namespace App\Models;

use App\Traits\HasApproval;
use App\Traits\ModelHelpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestVoid extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasApproval;
    use ModelHelpers;

    protected $fillable = [
        'request_type',
        'request_id',
        'reason_for_void',
        'request_status',
        'approvals',
        'created_by',
    ];
    protected $casts = [
        "approvals" => 'array'
    ];
}

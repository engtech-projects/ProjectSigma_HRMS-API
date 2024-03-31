<?php

namespace App\Models;

use App\Models\Traits\HasAttendanceLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;
    use HasAttendanceLog;

    protected $fillable = [
        'code',
        'project_monitoring_id',
    ];

    protected $cast = [
        'code' => 'string',
        'project_monitoring_id' => 'integer',
    ];


    /** MODEL
     * RELATED
     * RELATION
     */


    /**
     * MODEL
      LOCAL
      SCOPES
     */
}

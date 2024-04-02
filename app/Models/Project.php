<?php

namespace App\Models;

use App\Models\Traits\HasProjectMember;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasProjectMember;

    protected $fillable = [
        'code',
        'project_monitoring_id',
    ];

    protected $cast = [
        'code' => 'string',
        'project_monitoring_id' => 'integer',
    ];

    /**
     * MODEL
     * RELATED
     * RELATION
     */
}

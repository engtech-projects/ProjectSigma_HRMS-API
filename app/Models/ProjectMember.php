<?php

namespace App\Models;

use App\Models\Traits\HasDepartment;
use App\Models\Traits\HasEmployee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectMember extends Model
{
    use HasFactory, SoftDeletes;
    use HasDepartment, HasEmployee;

    protected $fillable = [
        'project_id',
        'employee_id',
    ];
}

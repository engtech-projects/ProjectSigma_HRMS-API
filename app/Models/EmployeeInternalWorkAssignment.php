<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeInternalWorkAssignment extends Model
{
    use HasFactory;
    use HasFactory;
    use SoftDeletes;

    public const INTERNAL_WORK_ASSIGNMENT = 'App\Model\InternalWorkExperience';
    protected $fillable = [
        'id',
        'internal_work_experience_id',
        'work_assignment_type',
        'work_assignment_id',
    ];
    public function employee_internalwork_assignment()
    {
        return $this->morphedByMany(EmployeeInternalWorkAssignment::INTERNAL_WORK_ASSIGNMENT, 'work_assignment');
    }
}

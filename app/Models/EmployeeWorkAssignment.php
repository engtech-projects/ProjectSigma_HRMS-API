<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeWorkAssignment extends Model
{
    use HasFactory;
    use HasFactory;
    use SoftDeletes;
    const INTERNAL_WORK_ASSIGNMENT = 'App\Model\InternalWorkExperience';
    const PAN_WORK_ASSIGNMENT = 'App\Model\EmployeePanRequest';
    protected $fillable = [
        'id',
        'internal_work_experience_id',
        'work_assignment_type',
        'work_assignment_id',
    ];
    public function employee_internalwork_assignment() {
        return $this->morphedByMany(EmployeeWorkAssignment::INTERNAL_WORK_ASSIGNMENT, 'assignment');
    }
    public function pan_work_assignment() {
        return $this->morphedByMany(EmployeeWorkAssignment::PAN_WORK_ASSIGNMENT, 'assignment');
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeePanWorkAssignment extends Model
{
    use HasFactory;
    use HasFactory;
    use SoftDeletes;

    public const PAN_WORK_ASSIGNMENT = 'App\Model\EmployeePanRequest';
    protected $fillable = [
        'id',
        'employee_pan_request_id',
        'work_assignment_type',
        'work_assignment_id',
    ];

    public function pan_work_assignment()
    {
        return $this->morphedByMany(EmployeePanWorkAssignment::PAN_WORK_ASSIGNMENT, 'work_assignment');
    }

}

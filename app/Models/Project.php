<?php

namespace App\Models;

use App\Models\Traits\HasProjectEmployee;
use App\Models\Traits\HasProjectMember;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasProjectEmployee;

    protected $fillable = [
        'code',
        'project_monitoring_id',
        'project_code',
        'status'
    ];

    protected $casts = [
        'project_code' => 'string',
        'project_monitoring_id' => 'integer',
        'created_at' => 'datetime:Y-m-d'
    ];

    /**
     * MODEL
     * RELATED
     * RELATION
     */
    public function employee_allowance(): MorphOne
    {
        return $this->morphOne(EmployeeAllowances::class, 'charge_assignment');
    }
}

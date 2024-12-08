<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeePanRequestProjects extends Model
{
    use HasFactory;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'id',
        'employee_pan_request_id',
        'project_id',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
    public function employeePanRequest(): BelongsTo
    {
        return $this->belongsTo(EmployeePanRequest::class);
    }

}

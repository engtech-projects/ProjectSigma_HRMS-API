<?php

namespace App\Models;

use App\Enums\InternalWorkExpStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Department extends Model
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;



    protected $fillable = [
        'id',
        'department_name',
        'updated_at',
        'created_at',
    ];


    /**
     * MODEL
     * RELATED
     * RELATION
     */

    public function internal_work_exp(): HasMany
    {
        return $this->hasMany(InternalWorkExperience::class, 'department_id', 'id');
    }

    public function employee_allowance(): MorphOne
    {
        return $this->morphOne(EmployeeAllowances::class, 'charge_assignment');
    }

    public function schedule(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'internal_work_exp')
            ->withPivot([
                'department_id',
                'employee_id'
            ])
            ->where("status", InternalWorkExpStatus::CURRENT->value)
            ->withtimestamps();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalaryGradeLevel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "salary_grade_levels";
    protected $primaryKey = "id";
    protected $fillable = [
        "salary_grade_level",
    ];

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($salaryGradeLevel) {
            $salaryGradeLevel->salary_grade_step()->delete();
        });
    }

    public function salary_grade_step(): HasMany
    {
        return $this->hasMany(SalaryGradeStep::class, 'salary_grade_level_id', 'id');
    }
}

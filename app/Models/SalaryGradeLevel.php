<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalaryGradeLevel extends Model
{
    use HasFactory;
    protected $table = "salary_grade_levels";
    protected $primaryKey = "id";
    protected $fillable = [
        "salary_grade_level",
    ];

    public function salary_grade_step(): HasMany
    {
        return $this->hasMany(SalaryGradeStep::class);
    }
}

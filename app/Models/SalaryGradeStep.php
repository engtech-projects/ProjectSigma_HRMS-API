<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalaryGradeStep extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        "step_name",
        "monthly_salary_amount",
        "salary_grade_level_id",
    ];

    public function salary_grade_level(): BelongsTo
    {
        return $this->belongsTo(SalaryGradeLevel::class);
    }
}

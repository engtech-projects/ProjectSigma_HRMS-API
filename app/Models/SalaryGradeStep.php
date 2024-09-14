<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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

    protected $hidden = [
        "monthly_salary_amount"
    ];

    public function salary_grade_level(): BelongsTo
    {
        return $this->belongsTo(SalaryGradeLevel::class)->withTrashed();
    }

    protected function dailyRate(): Attribute
    {
        return Attribute::make(
            get: fn () => round($this->monthly_salary_amount / 26, 2),
        );
    }

    protected function latePerHourDeduction(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->dailyRate / 8,
        );
    }

    protected function latePerMinDeduction(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->latePerHourDeduction / 60,
        );
    }
}

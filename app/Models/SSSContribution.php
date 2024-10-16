<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class SSSContribution extends Model
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    protected $table = 'sss_contributions';

    protected $fillable = [
        'id',
        'range_from',
        'range_to',
        'employee_share',
        'employer_share',
        'employee_compensation',
        'employer_compensation',
        'employee_wisp',
        'employer_wisp',
    ];


    public static function getContribution($salary)
    {
        return self::where('range_from', '<=', $salary)
            ->where('range_to', '>=', $salary)
            ->first();
    }
    public function contribution($salary)
    {
        return self::where('range_from', '<=', $salary)
            ->where('range_to', '>=', $salary)
            ->first();
    }
    public function deduction()
    {
        return $this->morphOne(PayrollDetailDeduction::class, 'deduction');
    }
}

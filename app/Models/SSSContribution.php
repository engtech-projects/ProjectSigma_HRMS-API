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
        'employee_contribution',
        'employer_contribution',
    ];


    public static function getContribution($salary)
    {
        return self::where('range_from', '<=', $salary)
            ->where('range_to', '>=', $salary)
            ->first();
    }
    public function sssContribution($salary)
    {
        return self::where('range_from', '<=', $salary)
            ->where('range_to', '>=', $salary)
            ->first();
    }

    public function contribution($salary)
    {
        $contribution = $this->sssContribution($salary);
        return [
            "employee" => $contribution->employee_contribution,
            "employer" => $contribution->employer_share,
        ];
    }
    public function compensation($salary)
    {
        $contribution = $this->sssContribution($salary);
        return [
            "employee" => $contribution->employee_share,
            "employer" => $contribution->employer_share
        ];
    }
    public function deduction()
    {
        return $this->morphOne(PayrollDetailDeduction::class, 'deduction');
    }
}

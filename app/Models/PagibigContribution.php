<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class PagibigContribution extends Model
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    protected $fillable = [
        'id',
        'range_from',
        'range_to',
        'employee_share_percent',
        'employer_share_percent',
        'employer_maximum_contribution',
        'employee_maximum_contribution',
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
}

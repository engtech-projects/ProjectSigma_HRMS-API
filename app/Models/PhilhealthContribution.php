<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class PhilhealthContribution extends Model
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'id',
        'range_from',
        'range_to',
        'employee_share',
        'employer_share',
        'share_type',
    ];

    public static function getContribution($salary)
    {
        return self::where('range_from', '<=', $salary)
            ->where('range_to', '>=', $salary)
            ->first();
    }

    public static function contribution($salary)
    {
        return self::where('range_from', '<=', $salary)
            ->where('range_to', '>=', $salary)
            ->first();
    }
}

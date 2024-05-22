<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class WitholdingTaxContribution extends Model
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'id',
        'range_from',
        'range_to',
        'term',
        'tax_base',
        'tax_amount',
        'tax_percent_over_base',
    ];

    public static function getContribution($salary)
    {
        return self::where('range_from', '<=', $salary)
            ->where('range_to', '>=', $salary)
            ->first();
    }

    public function with_holding_tax_deduction($salary)
    {
        $wht = WitholdingTaxContribution::getContribution($salary);
        $taxBase = $wht->tax_base;
        $taxAmount = $wht->tax_amount;
        $diff = abs($taxBase - $taxAmount);
        $total = ($wht->tax_percent_over_base / 100) * $diff + $taxAmount;
        return $total;
    }
}

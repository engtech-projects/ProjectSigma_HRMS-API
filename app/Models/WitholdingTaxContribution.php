<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class WitholdingTaxContribution extends Model
{
    use HasApiTokens, HasFactory, Notifiable,SoftDeletes;
    
    protected $fillable = [
        'id',
        'range_from',
        'range_to',
        'term',
        'tax_base',
        'tax_amount',
        'tax_percent_over_base',
    ];
}

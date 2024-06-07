<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollDetailDeduction extends Model
{
    use HasFactory;
    protected $table = "payroll_details_deductions";
    protected $fillable = [
        "type",
        'payroll_details_id',
        'deduction_type',
        'deduction_id',
        'name',
        'amount',
    ];

    public function deduction()
    {
        return $this->morphTo();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PayrollDetailDeduction extends Model
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    protected $table = "payroll_details_deductions";
    protected $fillable = [
        "type",
        'payroll_details_id',
        'deduction_type',
        'deduction_id',
        'name',
        'amount',
    ];

    public function deduction(): MorphTo
    {
        return $this->morphTo("deduction");
    }
}

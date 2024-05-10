<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollDetailDeduction extends Model
{
    use HasFactory;

    public function deduction()
    {
        return $this->morphTo();
    }
}

<?php

namespace App\Models\Traits;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasEmployee
{
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

}

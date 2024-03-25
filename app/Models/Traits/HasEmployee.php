<?php

namespace App\Models\Traits;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasEmployee
{
    /**
     * Get the user that owns the HasEmployee
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }
}

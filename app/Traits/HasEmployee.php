<?php

namespace App\Traits;

use App\Models\Employee;

trait HasEmployee
{
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }
}

<?php

namespace App\Models\Traits;

use App\Models\AttendanceLog;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasAttendanceLog
{
    public function attendance_logs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class);
    }
}

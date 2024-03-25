<?php

namespace App\Http\Services;

use App\Models\AttendanceLog;

class FailureToLogService
{
    protected $attendanceLog;
    public function __construct(AttendanceLog $attendanceLog)
    {
        $this->attendanceLog = $attendanceLog;
    }

    public function getAll()
    {
        return $this->attendanceLog->all();
    }

    public function get(AttendanceLog $attendanceLog)
    {
    }

    public function create(array $attributes)
    {

    }

    public function update(array $attributes, AttendanceLog $attendanceLog)
    {
    }

    public function delete(AttendanceLog $attendanceLog)
    {
    }
}

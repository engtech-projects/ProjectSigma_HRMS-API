<?php

namespace App\Http\Services;

use App\Exceptions\TransactionFailedException;
use App\Models\AttendanceLog;
use Carbon\Carbon;

class AttendanceLogService
{
    protected $log;
    public function __construct(AttendanceLog $log)
    {
        $this->log = $log;
    }

    public function getAll()
    {
        return $this->log->with(['project', 'department'])->get();
    }

    public function getAllToday()
    {
        return $this->log->where('date', Carbon::now()->format('Y-m-d'))->with(['project', 'department', 'employee'])->get();
    }

    public function get(AttendanceLog $log)
    {
        return $log;
    }

    public function create(array $attributes)
    {
        try {
            $this->log->create($attributes);
        } catch (\Exception $e) {
            throw new TransactionFailedException("Create transaction failed.", 500, $e);
        }
    }

    public function update(array $attributes, AttendanceLog $attendanceLog)
    {
        try {
            $attendanceLog->update($attributes);
        } catch (\Exception $e) {
            throw new TransactionFailedException("Update transaction failed.", 500, $e);
        }
    }

    public function delete(AttendanceLog $attendanceLog)
    {
        try {
            $attendanceLog->delete();
        } catch (\Exception $e) {
            throw new TransactionFailedException("Delete transaction failed.", 500, $e);
        }
    }

    public function getEmployeeAttendance($employeeId)
    {
        $attendances = AttendanceLog::whereIn('employee_id', $employeeId)->get();

        return $attendances;
    }
}

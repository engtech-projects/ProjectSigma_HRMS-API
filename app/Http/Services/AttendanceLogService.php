<?php

namespace App\Http\Services;

use App\Enums\AttendanceType;
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
        return $this->log->with(['project', 'department', 'employee'])->get();
    }
    public function getAllToday()
    {
        return $this->log->where('date', Carbon::now()->format('Y-m-d'))->with(['project', 'department', 'employee'])->orderBy('created_at', 'DESC')->get();
    }
    public function getFilterDateAndEmployee($request)
    {
        $query = $this->log->query();
        if ($request->date) {
            $query->where('date', $request->date);
        }
        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->department_id) {
            $query->where('department_id', $request->department_id);
        }
        if ($request->project_id) {
            $query->where('project_id', $request->project_id);
        }
        if ($request->attendance_type && $request->attendance_type != AttendanceType::ALL->value) {
            $query->where('attendance_type', $request->attendance_type);
        }
        return $query->with(['project', 'department', 'employee', 'portal'])->withTrashed()->orderBy('created_at', 'DESC')->paginate(10);
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

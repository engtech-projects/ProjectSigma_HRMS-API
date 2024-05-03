<?php

namespace App\Http\Traits;

use App\Helpers;
use App\Models\Employee;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

trait DailyTimeRecord
{
    use Attendance;
    public function generateDTR(Employee $employee, array $period = [])
    {
        return $this->mapEmployeeAttendance($employee->attendance_log);
    }
    private function mapEmployeeAttendance(Collection $attendances)
    {
        $result = $attendances->groupBy('date')->map(function ($groupDate) {
            return $groupDate->groupBy(function ($value) {
                $time = Carbon::parse($value["time"]);
                return $time->hour <= 12 ? "Morning" : "Afternoon";
            })->map(function ($groupTime) {
                return collect($groupTime)->groupBy('log_type')->map(function ($value) {
                    return collect($value)->map(function ($value) {
                        return [
                            "time" => $value->time,
                            "type" => $value->attendance_type,
                            "log_type" => $value->log_type
                        ];
                    });
                });
            });
        })->map(function ($attendance) {
            $attendance["total"] = $this->getTotalOfHours($attendance);
            return $attendance;
        });
        return $result;
    }
    private function getTotalOfHours($attendances)
    {
        $totalHours = 0;
        $totalDays = 0;
        $totalMinutes = 0;
        foreach ($attendances as $key => $attendance) {
            $interval = $this->calculateAttendanceLog($attendance);
            $totalHours += $interval->totalHours;
            $totalDays += $interval->totalDays;
            $totalMinutes += $interval->totalMinutes;
        }
        return [
            "hours" => $totalHours,
            "days" => $totalDays,
            "minutes" => $totalMinutes,
        ];
    }
}

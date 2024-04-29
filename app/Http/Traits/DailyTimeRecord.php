<?php

namespace App\Http\Traits;

use App\Models\Employee;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

trait DailyTimeRecord
{
    use Attendance;
    public function generateDTR(Employee $employee, array $period = [])
    {
        return [

            "regular_time" => $this->mapEmployeeAttendance($employee->attendance_log),
            "over_time" => [],
        ];
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
                            "type" => $value->attendance_type
                        ];
                    });
                });
            });
        })->map(function ($attendance){
            $attendance["total_hours"] = $this->getTotalOfHours($attendance);
            return $attendance;
        });
        return $result;
    }
    private function getTotalOfHours($attendances)
    {
        foreach ($attendances as $key => $attendance) {
            return $this->calculateAttendanceLog($attendance);
        }
    }
}

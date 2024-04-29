<?php

namespace App\Http\Traits;

use App\Models\Employee;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

trait DailyTimeRecord
{
    public function generateDTR(Employee $employee, array $period = [])
    {
        return [
            "attendance" => [
                "regular_time" => $this->mapEmployeeAttendance($employee->attendance_log),
                "over_time" => [],
            ]
        ];
    }
    private function mapEmployeeAttendance(Collection $attendances)
    {
        $result = $attendances->groupBy(function ($value) {
            $time = Carbon::parse($value->time);
            return (int)$time->hour() < 12  ? "Morning" : "Afternoon";
        })->map(function ($values) {
            return collect($values)->groupBy('log_type');
        });
        return $result;
    }
}

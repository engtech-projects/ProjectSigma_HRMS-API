<?php

namespace App\Http\Controllers\Actions\Employee;

use App\Helpers;
use App\Http\Services\EmployeeService;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
class CountAbsentLateController extends Controller
{

    public function __invoke()
    {
        if (!Cache::has('lateAndAbsent')) {
            $periodDates = Helpers::dateRange([
                'period_start' => Carbon::now()->startOfMonth(),
                'period_end' => Carbon::now()->lastOfMonth()
            ]);
            $employee = Employee::get();
            $employeeChartData = $employee->map(function($employee) use ($periodDates) {
                $absent_count = 0;
                $late_count = 0;
                foreach ($periodDates as $period) {
                    $sched = $employee->applied_schedule_with_attendance($period['date']);
                    if (!$sched) {
                        continue;
                    }
                    foreach($sched as $schd ) {
                        if (!$schd['applied_ins'] || !$schd['applied_outs']) {
                            $absent_count ++;
                        }
                    }
                }
                return [
                    'id'=> $employee->id,
                    'fullname_first'=> $employee->fullname_first,
                    'profile_photo'=> $employee->profile_photo,
                    'absent_count' => $absent_count,
                    'late_count' => $late_count,
                ];
            })->where('absent_count', '>', 0)->where('late_count', '>', 0);

            $chartData = [
                'absent' => $employeeChartData->sum('absent_count'),
                'late' => $employeeChartData->sum('absent_count'),
                'employees' => $employeeChartData,
            ];
            Cache::store('database')->put('lateAndAbsent', $chartData, 864000);
        }
        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully fetched.',
            'data' => Cache::get('dashboard'),
        ]);
    }
}

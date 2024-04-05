<?php

namespace App\Http\Controllers\Actions\Attendance;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterDtrRequest;
use App\Http\Resources\AttendanceLogResource;
use App\Http\Resources\EmployeeLeaveResource;
use App\Http\Resources\InternalWorkExpResource;
use App\Http\Resources\OvertimeResource;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;


class EmployeeDtrController extends Controller
{

    protected $employee;
    public function __construct(Employee $employee)
    {
        $this->employee = $employee;
    }
    /**
     * Handle the incoming request.
     */
    public function __invoke(FilterDtrRequest $request)
    {
        $data = $this->getSchedule($request->validated());
        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully fetched.',
            'data' => $data

        ]);
    }

    private function getSchedule(array $filter = [])
    {
        $employee = $this->employee->with(['employee_internal', 'employee_overtime', 'employee_has_projects', 'employee_schedule' => function ($query) use ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('startRecur', '>=', $filter['start_date']);
            });
        }])->where('id', $filter['employee_id'])
            ->first();

        $schedule = $this->mapResultSchedule($employee);
        return $schedule;
    }

    private function mapResultSchedule($employee)
    {
        $employee->employee_schedule = collect($employee["employee_schedule"])->groupBy(function ($schedule) use ($employee) {
            $dateSchedule = $schedule->startRecur->format('F j, Y');
            return $dateSchedule;
        })->toArray();
        $employee->employee_leave = $employee->employee_leave->where('with_pay', true);

        $employee->employee_schedule = collect($employee->employee_schedule)->map(function ($schedule) {
            return $schedule;
        });


        $employee->employee_schedule = $this->filterScheduleByGroupType($employee->employee_schedule);
        return [
            "id" => $employee->id,
            "employee_name" => $employee->fullname_first,
            "employee_internal_exp" => InternalWorkExpResource::collection($employee->employee_internal),
            "employee_schedule" => $employee->employee_schedule,
            "attendance" => AttendanceLogResource::collection($employee->attendance_log),
            "leave" => EmployeeLeaveResource::collection($employee->employee_leave),
            "overtime" => OvertimeResource::collection($employee->employee_overtime),
            "employee_projects" => $employee->employee_has_projects,
        ];
        return $employee->employee_schedule;
    }

    private function filterScheduleByType($schedule)
    {

        $schedule = collect($schedule)->map(function ($val) use ($schedule) {
            $hasIrregSchedule = collect($val)->contains('scheduleType', 'Irregular');
            $sched = collect($val);
            return $hasIrregSchedule ? $sched->where('scheduleType', 'Irregular')->values() : $sched->where('scheduleType', 'Regular')->values();
        })->all();
        return array_filter($schedule);
    }

    private function filterScheduleByGroupType($schedule)
    {
        $newSched = collect($schedule)->map(function ($schedule) {

            $groupSched = collect($schedule)->groupBy('groupType');

            $groupSched = $this->filterScheduleByType($groupSched);
            return $groupSched;
        })->all();
        return array_filter($newSched);
    }
}

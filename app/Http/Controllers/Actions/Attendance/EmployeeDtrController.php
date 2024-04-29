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
        $employee = $this->employee->with(['employee_internal', 'employee_leave', 'employee_overtime', 'employee_has_projects', 'employee_schedule' => function ($query) use ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('startRecur', '>=', $filter['start_date']);
            });
        }])->where('id', $filter['employee_id'])->first();

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


        $employee->employee_schedule = $this->filterSchedule($employee->employee_schedule);
        return [
            "id" => $employee->id,
            "employee_name" => $employee->fullname_first,
            "employee_internal_exp" => InternalWorkExpResource::collection($employee->employee_internal),

            "employee_schedule" => collect($employee->employee_schedule)->map(function ($schedule) use ($employee) {
                return collect($schedule)->map(function ($value) use ($employee) {
                    return [
                        "schedule" => $value,
                        "logs" => AttendanceLogResource::collection($employee->attendance_log),
                        "leaves" => EmployeeLeaveResource::collection($employee->employee_leave),
                        "overtime" => OvertimeResource::collection($employee->employee_overtime),
                    ];
                });
            }),
            "employee_projects" => $employee->employee_has_projects,
        ];

        return $employee->employee_schedule;
    }

    private function filterSchedule($schedule)
    {
        $employeeSchedule = collect($schedule)->map(function ($schedule) {
            $hasIrregSchedule = collect($schedule)->contains('scheduleType', 'Irregular');
            $employeeGroupFiltered = collect($schedule)->filter(function ($value) use ($hasIrregSchedule) {
                if ($hasIrregSchedule) {
                    return $value["groupType"] == 'employee' && $value["scheduleType"] === 'Irregular';
                }
                return  $value["groupType"] == 'employee' &&  $value["scheduleType"] === 'Regular';
            })->values();

            if (empty($employeeGroupFiltered)) {
                $employeeGroupFiltered = collect($schedule)->filter(function ($value) {
                    return $value["groupType"] != 'employee';
                })->all();
            }
            return $employeeGroupFiltered->map(function ($schedule) {
                return [
                    "groupType" => $schedule["groupType"],
                    "scheduleType" => $schedule["scheduleType"],
                    "daysOfWeek" => $schedule["daysOfWeek"],
                    "startTime" => $schedule["startTime"],
                    "endTime" => $schedule["endTime"],
                    "startRecur" => $schedule["startRecur"],
                    "endRecur" => $schedule["endRecur"],
                ];
            });
        })->all();
        return array_filter($employeeSchedule);
    }
}

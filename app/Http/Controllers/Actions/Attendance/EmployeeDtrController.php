<?php

namespace App\Http\Controllers\Actions\Attendance;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterDtrRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Employee;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        $employee = $this->employee->with(['employee_internal', 'employee_has_projects', 'employee_schedule' => function ($query) use ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('startRecur', '>=', $filter['start_date']);
            });
        }])->where('id', $filter['employee_id'])
            ->first();

        dd($employee);

        $schedule = $this->mapResultSchedule($employee);
        return $schedule;
    }

    private function mapResultSchedule($employee)
    {
        $employee->employee_schedule = collect($employee["employee_schedule"])->groupBy(function ($schedule) use ($employee) {
            $dateSchedule = $schedule->startRecur->format('F j, Y');
            return $dateSchedule;
        })->toArray();

        $employee->employee_schedule = collect($employee->employee_schedule)->map(function ($schedule) {
            return $schedule;
        });

        /* $employee->employee_schedule = $this->filterScheduleByType($employee->employee_schedule); */


        return [
            "id" => $employee->id,
            "employee_name" => $employee->fullname_first,
            "employee_internal_exp" => $employee->employee_internal,
            "employee_schedule" => $employee->employee_schedule,
            "attendance" => $employee->attendance_log,
            "leave" => $employee->employee_leave,
            "overtime" => [],
            "employee_projects" => $employee->employee_has_projects,
        ];
    }

    private function filterScheduleByType($schedule)
    {
        $hasIrregularSchedule = collect($schedule)->flatten(1)->contains('scheduleType', 'Irregular');

        $schedule = collect($schedule)->map(function ($schedule) use ($hasIrregularSchedule) {
            return $hasIrregularSchedule ? collect($schedule)->where('scheduleType', 'Irregular')->all() : $schedule;
        })->toArray();
        $filterSchedule = array_filter($schedule);
        return $filterSchedule;
    }
}

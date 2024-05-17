<?php

namespace App\Http\Controllers;

use App\Enums\AssignTypes;
use App\Enums\AttendanceLogType;
use App\Enums\AttendanceType;
use App\Models\AttendanceLog;
use Illuminate\Http\JsonResponse;
use App\Utils\PaginateResourceCollection;
use App\Http\Services\AttendanceLogService;
use App\Http\Resources\AttendanceLogResource;
use App\Http\Requests\StoreAttendanceLogRequest;
use App\Http\Requests\StoreFacialAttendanceLog;
use App\Http\Requests\UpdateAttendanceLogRequest;
use App\Models\AttendancePortal;
use App\Models\Employee;
use App\Models\EmployeePattern;
use Carbon\Carbon;
use GuzzleHttp\Psr7\Request;

class AttendanceLogController extends Controller
{
    protected $attendanceLogService;
    public const DEPARTMENT = "App\Models\Department";
    public const PROJECT = "App\Models\Project";

    public function __construct(AttendanceLogService $attendanceLogService)
    {
        $this->attendanceLogService = $attendanceLogService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $attendanceLog = $this->attendanceLogService->getAll();
        $collection = collect(AttendanceLogResource::collection($attendanceLog));

        return new JsonResponse([
            "success" => true,
            "message" => "Successfully fetched.",
            "data" => PaginateResourceCollection::paginate($collection, 5)
        ], JsonResponse::HTTP_OK);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAttendanceLogRequest $request)
    {
        $this->attendanceLogService->create($request->validated());

        return new JsonResponse([
            "success" => true,
            "message" => "Successfully created.",
        ], JsonResponse::HTTP_CREATED);
    }
    public function getCurrentDate()
    {
        return new JsonResponse([
            "data" => Carbon::now()->format('Y-m-d'),
            "success" => true,
            "message" => "Successfully current-date fetch.",
        ], JsonResponse::HTTP_OK);
    }

    public function facialAttendance(StoreFacialAttendanceLog $request)
    {
        $val = $request->validated();
        if ($val) {
            $mainsave = new AttendanceLog();
            $token = $request->header("Portal_token");
            $main = AttendancePortal::with('assignment')->where('portal_token', $token)->first();
            $type = $main->assignment_type;
            $id = $main->assignment->id;
            switch ($type) {
                case AttendanceLogController::DEPARTMENT:
                    $type = AssignTypes::DEPARTMENT->value;
                    $mainsave->department_id = $id;
                    break;
                case AttendanceLogController::PROJECT:
                    $type = AssignTypes::PROJECT->value;
                    $mainsave->project_id = $id;
                    break;
            }
            $main->type = $type;
            $mainsave->date = Carbon::now()->format('Y-m-d');
            $mainsave->time = Carbon::now()->format('H:i:s');
            $mainsave->attendance_type = AttendanceType::FACIAL->value;
            $mainsave->fill($val);
            if ($mainsave->save()) {
                $employeeAttendanceLog = AttendanceLog::where([
                    ['employee_id', $request->employee_id],
                    ['date', Carbon::now()->format('Y-m-d')],
                ])->get();
                $employee = Employee::with('employee_schedule')->find($request->employee_id);
                $mainsave->schedule = $employee->applied_schedule(Carbon::now()->format('Y-m-d'));
                $mainsave->employee = $employee;
                $mainsave->employee_attendance_log = $employeeAttendanceLog;
                return new JsonResponse([
                    "success" => true,
                    "message" => "Successfully save.",
                    "data" => $mainsave,
                ], JsonResponse::HTTP_OK);
            }
            return new JsonResponse([
                "success" => false,
                "message" => "Failed save.",
            ], JsonResponse::HTTP_EXPECTATION_FAILED);
        }
        return new JsonResponse([
            "success" => false,
            "message" => "Failed save.",
        ], JsonResponse::HTTP_EXPECTATION_FAILED);
    }

    public function facialAttendanceList()
    {
        $main = EmployeePattern::get();
        if ($main) {
            return new JsonResponse([
                "success" => true,
                "message" => "Fetch Successfully.",
                "data" => $main,
            ], JsonResponse::HTTP_OK);
        }
        return new JsonResponse([
            "success" => false,
            "message" => "No data found.",
        ], JsonResponse::HTTP_EXPECTATION_FAILED);
    }

    /**
     * Display the specified resource.
     */
    public function show(AttendanceLog $log)
    {
        return new JsonResponse([
            "success" => true,
            "message" => "Successfully fetched.",
            "data" => new AttendanceLogResource($log),
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttendanceLogRequest $request, AttendanceLog $log)
    {
        $this->attendanceLogService->update($request->validated(), $log);

        return new JsonResponse([
            "success" => true,
            "message" => "Successfully updated.",
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AttendanceLog $log)
    {
        $this->attendanceLogService->delete($log);

        return new JsonResponse([
            "success" => true,
            "message" => "Successfully deleted.",
        ], JsonResponse::HTTP_OK);
    }

    public function getToday()
    {
        $attendanceLog = $this->attendanceLogService->getAllToday();
        $collection = collect(AttendanceLogResource::collection($attendanceLog));

        return new JsonResponse([
            "success" => true,
            "message" => "Successfully fetched.",
            "data" => PaginateResourceCollection::paginate(collect($collection), 15)
        ], JsonResponse::HTTP_OK);
    }
}

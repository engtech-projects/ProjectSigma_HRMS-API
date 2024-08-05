<?php

namespace App\Http\Controllers;

use App\Enums\AssignTypes;
use App\Enums\AttendanceLogType;
use App\Enums\AttendanceType;
use App\Http\Requests\AllAttendanceLogsRequest;
use App\Http\Requests\StoreQrAttendanceLog;
use App\Models\AttendanceLog;
use App\Models\CompanyEmployee;
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
use App\Models\Schedule;
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
            "data" => PaginateResourceCollection::paginate($collection, 15)
        ], JsonResponse::HTTP_OK);
    }

    public function allAttendanceLogs(AllAttendanceLogsRequest $request)
    {
        $attendanceLog = $this->attendanceLogService->getFilterDateAndEmployee($request);
        $collection = collect(AttendanceLogResource::collection($attendanceLog));
        return new JsonResponse([
            "success" => true,
            "message" => "Successfully fetched.",
            "data" => PaginateResourceCollection::paginate($collection, 15),
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
        $portalToken = $request->header("Portal_token", $request->bearerToken());
        $dateNow = Carbon::now()->format('Y-m-d');
        $timeNow = Carbon::now()->format('H:i:s');
        $val = $request->validated();
        if ($val) {
            $mainsave = new AttendanceLog();
            $main = AttendancePortal::with('assignment')->where('portal_token', $portalToken)->first();
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
            $mainsave->date = $dateNow;
            $mainsave->time = $timeNow;
            $mainsave->attendance_type = AttendanceType::FACIAL->value;
            $mainsave->fill($val);
            if ($mainsave->save()) {
                $employee = Employee::with('employee_schedule', 'profile_photo',)->find($val["employee_id"]);
                $return = [];
                $return['log_saved'] = $mainsave;
                $return['schedule'] = $employee->applied_schedule_with_attendance($dateNow);
                $return['employee'] = $employee;
                return new JsonResponse([
                    "success" => true,
                    "message" => "Successfully save.",
                    "data" => $return,
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
    public function qrAttendance(StoreQrAttendanceLog $request)
    {
        $dateNow = Carbon::now()->format('Y-m-d');
        $val = $request->validated();
        if ($val) {
            $currentTime = Carbon::now();
            $employeeCompany = CompanyEmployee::where('employeedisplay_id', $val['employee_code'])->first();
            $mainSave = new AttendanceLog();
            $mainSave->date = $dateNow;
            $mainSave->time = $currentTime->subMinutes($val['offset'])->format('H:i:s');
            $mainSave->attendance_type = AttendanceType::QR_CODE->value;
            $mainSave->log_type = $val['log_type'];
            $mainSave->employee_id = $employeeCompany->employee_id;
            if ($val['department_id']) {
                $mainSave->department_id = $val['department_id'];
            }else if ($val['project_id']) {
                $mainSave->project_id = $val['project_id'];
            }
            if ($mainSave->save()) {
                return new JsonResponse([
                    "success" => true,
                    "message" => "Successfully save.",
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

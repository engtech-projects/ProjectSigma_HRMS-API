<?php

namespace App\Http\Controllers;

use App\Enums\AccessibilityHrms;
use App\Enums\AssignTypes;
use App\Enums\AttendanceType;
use App\Enums\WorkLocation;
use App\Http\Requests\AllAttendanceLogsRequest;
use App\Http\Requests\StoreQrAttendanceLog;
use App\Models\AttendanceLog;
use App\Models\CompanyEmployee;
use Illuminate\Http\JsonResponse;
use App\Http\Services\AttendanceLogService;
use App\Http\Resources\AttendanceLogResource;
use App\Http\Requests\StoreAttendanceLogRequest;
use App\Http\Requests\StoreFacialAttendanceLog;
use App\Http\Requests\UpdateAttendanceLogRequest;
use App\Http\Resources\EmployeeSummaryCphotoResource;
use App\Http\Traits\CheckAccessibility;
use App\Models\AttendancePortal;
use App\Models\Employee;
use App\Models\EmployeePattern;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AttendanceLogController extends Controller
{
    use CheckAccessibility;
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
        return AttendanceLogResource::collection($attendanceLog)
        ->additional([
            'success' => true,
            'message' => 'Successfully fetched.',
        ]);
    }
    public function allAttendanceLogs(AllAttendanceLogsRequest $request)
    {
        $attendanceLog = $this->attendanceLogService->getFilterDateAndEmployee($request);
        return AttendanceLogResource::collection($attendanceLog)
        ->additional([
            'success' => true,
            'message' => "Successfully fetched.",
        ]);
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
    public function getCurrentDateTime()
    {
        return new JsonResponse([
            'date' => Carbon::now()->format('Y-m-d'),
            'time' => Carbon::now()->format('Y-m-d H:i:s'),
            "success" => true,
            "message" => "Successfully current-date fetch.",
        ], JsonResponse::HTTP_OK);
    }

    public function facialAttendance(StoreFacialAttendanceLog $request)
    {
        $logAttendance = config('app.log_attendance');
        if ($logAttendance) {
            Log::channel('attendance_log')->info('Attempt', ['request' => $request->all()]);
            $startTime = microtime(true);
        }
        $portalToken = $request->header("Portal_token", $request->bearerToken());
        $now = Carbon::now();
        $dateNow = $now->copy()->format('Y-m-d');
        $timeNow = $now->copy()->format('H:i:s');
        $val = $request->validated();
        // Check if Already Logged in/out within 15 mins before and after
        $lastLogSame = AttendanceLog::where([
            "employee_id" => $val["employee_id"],
            "date" => $dateNow,
            "log_type" => $val["log_type"],
        ])->whereBetween(
            "time",
            [$now->copy()->subMinutes(15)->format('H:i:s'), $now->copy()->addMinutes(15)->format('H:i:s')]
        )
        ->first();
        if ($lastLogSame) {
            return new JsonResponse([
                "success" => false,
                "message" => "Already Logged " . $lastLogSame->log_type . " on " . $lastLogSame->time_human,
            ], JsonResponse::HTTP_EXPECTATION_FAILED);
        }
        if ($logAttendance) {
            Log::channel('attendance_log')->info('Validated', ['request' => $request->all(), "time" => microtime(true) - $startTime]);
            $startTime = microtime(true);
        }
        $mainsave = new AttendanceLog();
        $mainsave->fill($val);
        if ($logAttendance) {
            Log::channel('attendance_log')->info('Filled', ['request' => $request->all(), "time" => microtime(true) - $startTime]);
            $startTime = microtime(true);
        }
        $main = AttendancePortal::with('assignment')->where('portal_token', $portalToken)->first();
        $mainsave->portal_id = $main->id;
        $type = $val["assignment_type"];
        $portalDepartmentId = $main->departments()->first()?->id;
        $portalProjectId = $main->projects()->first()?->id;
        if ($logAttendance) {
            Log::channel('attendance_log')->info('Portal Fetched', ['request' => $request->all(), "time" => microtime(true) - $startTime]);
            $startTime = microtime(true);
        }
        $employee = Employee::with([
            'employee_schedule',
            'profile_photo',
            'employee_internal.projects.project_schedule',
            'employee_internal.department.schedule',
            'current_employment.projects',
            'current_employment.department',
        ])
        ->find($val["employee_id"]);
        if ($logAttendance) {
            Log::channel('attendance_log')->info('Employee Fetched', ['request' => $request->all(), "time" => microtime(true) - $startTime]);
            $startTime = microtime(true);
        }
        // WHEN TYPE IS PROJECT THE SPECIFIED project_id WILL BE REQUIRED AND LOGGED IN THE ATTENDANCE AS CHARGED
        // WHEN TYPE IS DEPARTMENT THE SPECIFIED department_id WILL BE A PLACEHOLDER AS A LAST RESORT INCASE THE EMPLOYEE DOESN'T HAVE A DEPARTMENT OR PROJECT
        switch ($type) {
            case AssignTypes::DEPARTMENT->value:
                $latest_project = $employee->current_employment?->projects()->orderBy('id', 'desc')->first();
                if ($employee->current_employment?->work_location == WorkLocation::OFFICE->value) {
                    $mainsave->department_id = $employee->current_employment->department_id;
                } elseif ($employee->current_employment?->work_location == WorkLocation::PROJECT->value && $latest_project?->id) {
                    $mainsave->project_id = $latest_project->id;
                } else {
                    $mainsave->department_id = $portalDepartmentId;
                }
                break;
            case AssignTypes::PROJECT->value:
                if ($val["project_id"]) {
                    $mainsave->project_id = $val["project_id"];
                } else {
                    $mainsave->project_id = $portalProjectId;
                }
                break;
        }
        if ($logAttendance) {
            Log::channel('attendance_log')->info('Charging Location Fetched', ['request' => $request->all(), "time" => microtime(true) - $startTime]);
            $startTime = microtime(true);
        }
        $mainsave->date = $dateNow;
        $mainsave->time = $timeNow;
        $mainsave->attendance_type = AttendanceType::FACIAL->value;
        $saved = $mainsave->save();
        if ($logAttendance) {
            Log::channel('attendance_log')->info('Before Save', ['request' => $request->all(), "time" => microtime(true) - $startTime]);
            $startTime = microtime(true);
        }
        if (!$saved) {
            return new JsonResponse([
                "success" => false,
                "message" => "Failed save.",
            ], JsonResponse::HTTP_EXPECTATION_FAILED);
        }
        $responseData = [
            "log_saved" => $mainsave,
            "employee" => EmployeeSummaryCphotoResource::make($employee),
            "logs_today" => AttendanceLog::where('employee_id', $employee->id)->where('date', $dateNow)->get(),
        ];
        if ($logAttendance) {
            Log::channel('attendance_log')->info('Prepared Response', ['request' => $request->all(), "time" => microtime(true) - $startTime]);
            $startTime = microtime(true);
        }
        return new JsonResponse([
            "success" => true,
            "message" => "Successfully save.",
            "data" => $responseData
        ], JsonResponse::HTTP_OK);
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
            } elseif ($val['project_id']) {
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
        $main = EmployeePattern::whereHas("employee", function ($q) {
            $q->wherehas("current_employment");
        })
        ->get();
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
        if (!$this->checkUserAccess([AccessibilityHrms::HRMS_ATTENDANCE_ATTENDANCELOGSDELETE->value])) {
            return new JsonResponse([
                "success" => false,
                "message" => "You don't have permission to perform this action.",
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }
        if ($log->deleted_at) {
            return new JsonResponse([
                "success" => false,
                "message" => "Already deleted.",
            ], JsonResponse::HTTP_NOT_ACCEPTABLE);
        }
        $this->attendanceLogService->delete($log);
        return new JsonResponse([
            "success" => true,
            "message" => "Successfully deleted.",
        ], JsonResponse::HTTP_OK);
    }

    public function getToday()
    {
        $attendanceLog = $this->attendanceLogService->getAllToday();
        return AttendanceLogResource::collection($attendanceLog)
        ->additional([
            'success' => true,
            'message' => 'Successfully fetched.',
        ]);
    }
}

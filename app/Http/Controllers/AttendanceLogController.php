<?php

namespace App\Http\Controllers;

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
use Carbon\Carbon;

class AttendanceLogController extends Controller
{
    protected $attendanceLogService;

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
            "data" => PaginateResourceCollection::paginate(collect($collection), 15)
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

    public function facialAttendance(StoreFacialAttendanceLog $request)
    {
        $val = $request->validated();
        if ($val) {
            $main = new AttendanceLog();
            $main->fill($val);
            $main->date = Carbon::now()->format('Y-m-d');
            $main->time = Carbon::now()->format('H:i:s');
            $main->attendance_type = AttendanceType::FACIAL->value;
            if ($main->save()) {
                return new JsonResponse([
                    "success" => true,
                    "message" => "Successfully save.",
                    "data" => new AttendanceLogResource($main),
                ], JsonResponse::HTTP_OK);
            }
            return new JsonResponse([
                "success" => false,
                "message" => "Failed save.",
            ], JsonResponse::HTTP_EXPECTATION_FAILED);
        }
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
}

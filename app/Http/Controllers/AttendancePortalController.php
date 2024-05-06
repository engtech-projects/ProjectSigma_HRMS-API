<?php

namespace App\Http\Controllers;

use App\Enums\AssignTypes;
use App\Models\AttendancePortal;
use App\Http\Requests\StoreAttendancePortalRequest;
use App\Http\Requests\UpdateAttendancePortalRequest;
use Illuminate\Http\JsonResponse;

class AttendancePortalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAttendancePortalRequest $request)
    {
        $valData = $request->validated();
        try {
            if ($valData) {
                $data = new AttendancePortal();
                $type = $request["group_type"];
                switch ($type) {
                    case AssignTypes::DEPARTMENT->value:
                        $data->assignment_type = EmployeeAllowancesController::DEPARTMENT;
                        $data->assignment_id = $request["department_id"];
                        break;
                    case AssignTypes::PROJECT->value:
                        $data->assignment_type = EmployeeAllowancesController::PROJECT;
                        $data->assignment_id = $request["project_id"];
                        break;
                }
                $data->save();
                return new JsonResponse([
                    'success' => true,
                    'message' => 'Successfully save.',
                ], JsonResponse::HTTP_OK);
            }
        } catch (\Throwable $th) {
            return new JsonResponse([
                'success' => false,
                'error' => $th->getMessage(),
                'message' => 'Failed save.',
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AttendancePortal $attendancePortal)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AttendancePortal $attendancePortal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttendancePortalRequest $request, AttendancePortal $attendancePortal)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AttendancePortal $attendancePortal)
    {
        //
    }
}

<?php

namespace App\Http\Controllers;

use App\Enums\AssignTypes;
use App\Models\AttendancePortal;
use App\Http\Requests\StoreAttendancePortalRequest;
use App\Http\Requests\UpdateAttendancePortalRequest;
use Illuminate\Http\JsonResponse;

class AttendancePortalController extends Controller
{
    public const DEPARTMENT = "App\Models\Department";
    public const PROJECT = "App\Models\Project";
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $main = AttendancePortal::with('assignment')->paginate(15);
        if (!is_null($main)) {
            return new JsonResponse([
                'success' => true,
                'message' => 'Successfully fetch.',
                'data' => $main,
            ], JsonResponse::HTTP_OK);
        }
        return new JsonResponse([
            'success' => false,
            'message' => 'No data found.',
        ], 404);
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
                $data->fill($valData);
                $type = $request["group_type"];
                switch ($type) {
                    case AssignTypes::DEPARTMENT->value:
                        $data->assignment_type = AttendancePortalController::DEPARTMENT;
                        $data->assignment_id = $request["department_id"];
                        break;
                    case AssignTypes::PROJECT->value:
                        $data->assignment_type = AttendancePortalController::PROJECT;
                        $data->assignment_id = $request["project_id"];
                        break;
                }
                $data->ip_address = $request->ip();
                $data->save();
                return new JsonResponse([
                    'success' => true,
                    'message' => 'Successfully save.',
                    'data' => $data,
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
    public function show($id)
    {
        $main = AttendancePortal::find($id);
        if (!is_null($main)) {
            return new JsonResponse([
                'success' => true,
                'message' => 'Successfully fetch.',
                'data' => $main,
            ], JsonResponse::HTTP_OK);
        }
        return new JsonResponse([
            'success' => false,
            'message' => 'No data found.',
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttendancePortalRequest $request, $id)
    {
        $main = AttendancePortal::find($id);
        $valData = $request->validated();
        try {
            if ($valData) {
                if (!is_null($main)) {
                    $main->fill($valData);
                    $type = $request["group_type"];
                    switch ($type) {
                        case AssignTypes::DEPARTMENT->value:
                            $main->assignment_type = AttendancePortalController::DEPARTMENT;
                            $main->assignment_id = $request["department_id"];
                            break;
                        case AssignTypes::PROJECT->value:
                            $main->assignment_type = AttendancePortalController::PROJECT;
                            $main->assignment_id = $request["project_id"];
                            break;
                    }

                    if ($main->save()) {
                        return new JsonResponse([
                            'success' => true,
                            'message' => 'Successfully save.',
                        ], JsonResponse::HTTP_OK);
                    }
                }
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
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $main = AttendancePortal::find($id);
        if (!is_null($main)) {
            if ($main->delete()) {
                return new JsonResponse([
                    'success' => true,
                    'message' => 'Successfully delete.',
                ], JsonResponse::HTTP_OK);
            }
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed delete.',
            ], 400);
        }
        return new JsonResponse([
            'success' => false,
            'message' => 'No data found.',
        ], 404);
    }
}

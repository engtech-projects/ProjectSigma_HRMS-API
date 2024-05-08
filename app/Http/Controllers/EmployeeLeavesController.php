<?php

namespace App\Http\Controllers;

use App\Enums\RequestStatusType;
use App\Models\EmployeeLeaves;
use App\Http\Requests\StoreEmployeeLeavesRequest;
use App\Http\Requests\UpdateEmployeeLeavesRequest;
use App\Http\Resources\EmployeeLeaveResource;
use App\Http\Services\EmployeeLeaveService;
use App\Utils\PaginateResourceCollection;
use Illuminate\Http\JsonResponse;

class EmployeeLeavesController extends Controller
{
    protected $leaveRequestService;
    public function __construct(EmployeeLeaveService $leaveRequestService)
    {
        $this->leaveRequestService = $leaveRequestService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $main = $this->leaveRequestService->getAll();
        $paginated = EmployeeLeaveResource::collection($main);
        return new JsonResponse([
            'success' => true,
            'message' => 'LeaveForm Request fetched.',
            'data' => PaginateResourceCollection::paginate(collect($paginated), 15)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeLeavesRequest $request)
    {
        $main = new EmployeeLeaves();
        $valData = $request->validated();
        $data = json_decode('{}');

        if ($valData) {
            $main->fill($valData);
            $main->request_status = RequestStatusType::PENDING;

            if (!$main->save()) {
                $data->message = "Save failed.";
                $data->success = false;
                return response()->json($data, 400);
            }
            $data->message = "Successfully save.";
            $data->success = true;
            $data->data = $main;
            return response()->json($data, 400);
        }
        $data->message = "Save failed.";
        $data->success = false;
        return response()->json($data, 400);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $main = EmployeeLeaves::with('employee', 'department', 'project')->find($id);
        $data = json_decode('{}');
        if (!is_null($main)) {
            $data->message = "Successfully fetch.";
            $data->success = true;
            $data->data = $main;
            return response()->json($data);
        }
        $data->message = "No data found.";
        $data->success = false;
        return response()->json($data, 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeLeavesRequest $request, $id)
    {
        $main = EmployeeLeaves::find($id);
        $data = json_decode('{}');
        if (!is_null($main)) {
            $main->fill($request->validated());
            if ($main->save()) {
                $data->message = "Successfully update.";
                $data->success = true;
                $data->data = $main;
                return response()->json($data);
            }
            $data->message = "Update failed.";
            $data->success = false;
            return response()->json($data, 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $main = EmployeeLeaves::find($id);
        $data = json_decode('{}');
        if (!is_null($main)) {
            if ($main->delete()) {
                $data->message = "Successfully delete.";
                $data->success = true;
                $data->data = $main;
                return response()->json($data);
            }
            $data->message = "Failed delete.";
            $data->success = false;
            return response()->json($data, 400);
        }
        $data->message = "Failed delete.";
        $data->success = false;
        return response()->json($data, 404);
    }


    public function myRequests()
    {
        $myRequest = $this->leaveRequestService->getMyRequest();
        if ($myRequest->isEmpty()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'No data found.',
            ], JsonResponse::HTTP_OK);
        }
        return new JsonResponse([
            'success' => true,
            'message' => 'Leave Request fetched.',
            'data' => EmployeeLeaveResource::collection($myRequest)
        ]);
    }

    /**
     * Show can view all user approvals
     */
    public function myFormRequest()
    {
        $myApproval = $this->leaveRequestService->getMyLeaveForm();
        if ($myApproval->isEmpty()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'No data found.',
            ], JsonResponse::HTTP_OK);
        }
        return new JsonResponse([
            'success' => true,
            'message' => 'LeaveForm Request fetched.',
            'data' => EmployeeLeaveResource::collection($myApproval)
        ]);
    }

    /**
     * Show can view all pan request to be approved by logged in user (same login in manpower request)
     */
    public function myApprovals()
    {
        $myApproval = $this->leaveRequestService->getMyApprovals();
        if ($myApproval->isEmpty()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'No data found.',
            ], JsonResponse::HTTP_OK);
        }
        return new JsonResponse([
            'success' => true,
            'message' => 'LeaveForm Request fetched.',
            'data' => EmployeeLeaveResource::collection($myApproval)
        ]);
    }
}

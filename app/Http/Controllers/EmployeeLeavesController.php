<?php

namespace App\Http\Controllers;

use App\Enums\RequestStatuses;
use App\Http\Requests\AllLeaveRequestRequest;
use App\Http\Requests\ApprovalLeaveRequest;
use App\Http\Requests\MyLeaveRequest;
use App\Models\EmployeeLeaves;
use App\Http\Requests\StoreEmployeeLeavesRequest;
use App\Http\Requests\UpdateEmployeeLeavesRequest;
use App\Http\Resources\EmployeeLeaveResource;
use App\Http\Services\EmployeeLeaveService;
use App\Models\Users;
use App\Notifications\LeaveRequestForApproval;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

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
    public function index(AllLeaveRequestRequest $request)
    {
        $validatedData = $request->validated();
        $data = EmployeeLeaves::when($request->has('employee_id'), function ($query) use ($validatedData) {
            return $query->where('employee_id', $validatedData['employee_id']);
        })
        ->when($request->has('date_filter') && $validatedData['date_filter'] != '', function ($query) use ($validatedData) {
            return $query->where('date_of_absence_from', '<=', $validatedData['date_filter'])
                ->where('date_of_absence_to', '>=', $validatedData['date_filter']);
        })
        ->orderBy("created_at", "DESC")
        ->paginate(15);
        return EmployeeLeaveResource::collection($data)
        ->additional([
            'success' => true,
            'message' => 'Employee Leaves Request fetched.',
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
            $main->created_by = Auth::user()->id;
            $main->request_status = RequestStatuses::PENDING;

            if (!$main->save()) {
                $data->message = "Save failed.";
                $data->success = false;
                return response()->json($data, 400);
            }
            $main->refresh();
            if ($main->getNextPendingApproval()) {
                Users::find($main->getNextPendingApproval()['user_id'])->notify(new LeaveRequestForApproval($main));
            }
            $data->message = "Successfully save.";
            $data->success = true;
            $main = $main->refresh();
            $data->data = new EmployeeLeaveResource($main);
            return response()->json($data, 200);
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
        $main = EmployeeLeaves::with(['employee', 'department', 'project', 'leave'])->find($id);
        $data = json_decode('{}');
        if (!is_null($main)) {
            $data->message = "Successfully fetch.";
            $data->success = true;
            $data->data = new EmployeeLeaveResource($main);
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
    public function myRequests(MyLeaveRequest $request)
    {
        $validatedData = $request->validated();
        $data = EmployeeLeaves::when($request->has('employee_id'), function ($query) use ($validatedData) {
            return $query->where('employee_id', $validatedData['employee_id']);
        })
        ->when($request->has('date_filter') && $validatedData['date_filter'] != '', function ($query) use ($validatedData) {
            return $query->where('date_of_absence_from', '<=', $validatedData['date_filter'])
                ->where('date_of_absence_to', '>=', $validatedData['date_filter']);
        })
        ->with(['employee', 'department', 'project'])
        ->myRequests()
        ->orderBy("created_at", "DESC")
        ->paginate(15);
        return EmployeeLeaveResource::collection($data)
        ->additional([
            'success' => true,
            'message' => 'Employee Leaves Request fetched.',
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
            'message' => 'Leave Form Request fetched.',
            'data' => EmployeeLeaveResource::collection($myApproval)
        ]);
    }

    /**
     * Show can view all pan request to be approved by logged in user (same login in manpower request)
     */
    public function myApprovals(ApprovalLeaveRequest $request)
    {
        $validatedData = $request->validated();
        $data = EmployeeLeaves::when($request->has('employee_id'), function ($query) use ($validatedData) {
            return $query->where('employee_id', $validatedData['employee_id']);
        })
        ->when($request->has('date_filter') && $validatedData['date_filter'] != '', function ($query) use ($validatedData) {
            return $query->where('date_of_absence_from', '<=', $validatedData['date_filter'])
            ->where('date_of_absence_to', '>=', $validatedData['date_filter']);
        })
        ->with(['employee', 'department', 'project'])
        ->myApprovals()
        ->orderBy("created_at", "DESC")
        ->paginate(15);
        return EmployeeLeaveResource::collection($data)
        ->additional([
            'success' => true,
            'message' => 'Employee Leaves Request fetched.',
        ]);
    }
}

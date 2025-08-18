<?php

namespace App\Http\Controllers;

use App\Enums\StringRequestApprovalStatus;
use App\Http\Requests\OvertimeMyApprovalRequest;
use App\Http\Requests\OvertimeMyRequestRequest;
use App\Http\Requests\OvertimeRequest;
use App\Models\Overtime;
use App\Http\Requests\StoreOvertimeRequest;
use App\Http\Requests\UpdateOvertimeRequest;
use App\Http\Resources\OvertimeResource;
use App\Http\Services\OvertimeService;
use App\Notifications\OvertimeRequestForApproval;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class OvertimeController extends Controller
{
    protected $RequestService;
    public function __construct(OvertimeService $RequestService)
    {
        $this->RequestService = $RequestService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(OvertimeRequest $request)
    {
        $validatedData = $request->validated();
        $data = Overtime::when($request->has('employee_id'), function ($query) use ($validatedData) {
            return $query->whereHas('employees', function ($query2) use ($validatedData) {
                $query2->where('employee_id', $validatedData["employee_id"]);
            });
        })
        ->when($request->has('date_filter') && $validatedData['date_filter'] != '', function ($query) use ($validatedData) {
            return $query->whereDate('overtime_date', $validatedData['date_filter']);
        })
        ->with('employees')
        ->orderBy("created_at", "DESC")
        ->paginate(config("app.pagination_per_page", 10));
        return OvertimeResource::collection($data)
        ->additional([
            'success' => true,
            'message' => 'All Overtime Request fetched.',
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOvertimeRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $main = new Overtime();
                $validData = $request->validated();
                $main->fill($validData);
                $main->request_status = StringRequestApprovalStatus::PENDING;
                $main->created_by = auth()->user()->id;
                $main->save();
                $main->employees()->attach($validData["employees"]);
                $main->refresh();
                $main->notifyNextApprover(OvertimeRequestForApproval::class);
            });
            return new JsonResponse([
                'success' => true,
                'message' => 'Successfully save.',
            ], JsonResponse::HTTP_OK);
        } catch (\Throwable $th) {
            return new JsonResponse([
                'success' => false,
                "error" => $th,
                'message' => 'Save failed.',
            ], 400);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $main = Overtime::with("employees", "department", "project")->find($id);
        $data = json_decode('{}');
        if (!is_null($main)) {
            $data->message = "Successfully fetch.";
            $data->success = true;
            $data->data = new OvertimeResource($main);
            return response()->json($data);
        }
        $data->message = "No data found.";
        $data->success = false;
        return response()->json($data, 404);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOvertimeRequest $request, $id)
    {
        $main = Overtime::find($id);
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
        $data->message = "Failed update.";
        $data->success = false;
        return response()->json($data, 404);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $main = Overtime::find($id);
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
    public function myRequests(OvertimeMyRequestRequest $request)
    {
        $validatedData = $request->validated();
        $data = Overtime::when($request->has('employee_id'), function ($query) use ($validatedData) {
            return $query->whereHas('employees', function ($query2) use ($validatedData) {
                $query2->where('employee_id', $validatedData["employee_id"]);
            });
        })
        ->when($request->has('date_filter') && $validatedData['date_filter'] != '', function ($query) use ($validatedData) {
            return $query->whereDate('overtime_date', $validatedData['date_filter']);
        })
        ->myRequests()
        ->orderBy("created_at", "DESC")
        ->paginate(config("app.pagination_per_page", 10));
        return OvertimeResource::collection($data)
        ->additional([
            'success' => true,
            'message' => 'All Overtime Request fetched.',
        ]);
    }
    /**
     * Show can view all pan request to be approved by logged in user (same login in manpower request)
     */
    public function myApprovals(OvertimeMyApprovalRequest $request)
    {
        $validatedData = $request->validated();
        $data = Overtime::when($request->has('employee_id'), function ($query) use ($validatedData) {
            return $query->whereHas('employees', function ($query2) use ($validatedData) {
                $query2->where('employee_id', $validatedData["employee_id"]);
            });
        })
        ->when($request->has('date_filter') && $validatedData['date_filter'] != '', function ($query) use ($validatedData) {
            return $query->whereDate('overtime_date', $validatedData['date_filter']);
        })
        ->with(['employees', 'department', 'project'])
        ->myApprovals()
        ->orderBy("created_at", "DESC")
        ->paginate(config("app.pagination_per_page", 10));
        return OvertimeResource::collection($data)
        ->additional([
            'success' => true,
            'message' => 'All Overtime Request fetched.',
        ]);
    }
}

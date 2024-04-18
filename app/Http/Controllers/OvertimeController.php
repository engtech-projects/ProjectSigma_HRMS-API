<?php

namespace App\Http\Controllers;

use App\Enums\RequestStatusType;
use App\Enums\StringRequestApprovalStatus;
use App\Models\Overtime;
use App\Http\Requests\StoreOvertimeRequest;
use App\Http\Requests\UpdateOvertimeRequest;
use App\Http\Resources\OvertimeResource;
use App\Http\Services\OvertimeService;
use App\Models\OvertimeEmployees;
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
    public function index()
    {
        $main = Overtime::with("employee", "department", "project")->paginate(15);
        $data = json_decode('{}');
        $data->message = "Successfully fetch.";
        $data->success = true;
        $data->data = $main;
        return response()->json($data);
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
                $main->prepared_by = auth()->user()->id;
                $main->request_status = StringRequestApprovalStatus::PENDING;
                $main->save();
                $main->employees->attach($validData["employees"]);
                $main->save();
            });
            $data = json_decode('{}');
            $data->message = "Successfully save.";
            $data->success = true;
            return response()->json($data);
        } catch (\Throwable $th) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Save failed.',
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $main = Overtime::with("employee", "department", "project")->find($id);
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

    public function myRequests()
    {
        $myRequest = $this->RequestService->getMyRequest();
        if ($myRequest->isEmpty()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'No data found.',
            ], JsonResponse::HTTP_OK);
        }
        return new JsonResponse([
            'success' => true,
            'message' => 'Leave Request fetched.',
            'data' => OvertimeResource::collection($myRequest)
        ]);
    }

    /**
     * Show can view all pan request to be approved by logged in user (same login in manpower request)
     */
    public function myApprovals()
    {
        $myApproval = $this->RequestService->getMyApprovals();
        if ($myApproval->isEmpty()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'No data found.',
            ], JsonResponse::HTTP_OK);
        }
        return new JsonResponse([
            'success' => true,
            'message' => 'Cash Advance Request fetched.',
            'data' => OvertimeResource::collection($myApproval)
        ]);
    }
}

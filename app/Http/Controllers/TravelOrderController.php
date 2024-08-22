<?php

namespace App\Http\Controllers;

use App\Enums\StringRequestApprovalStatus;
use App\Http\Requests\AllTravelRequest;
use App\Http\Requests\TravelApprovalRequest;
use App\Http\Requests\TravelRequest;
use App\Models\TravelOrder;
use App\Http\Requests\StoreTravelOrderRequest;
use App\Http\Requests\UpdateTravelOrderRequest;
use App\Http\Resources\TravelOrderResource;
use App\Http\Services\TravelOrderService;
use App\Models\Users;
use App\Notifications\TravelRequestForApproval;
use App\Utils\PaginateResourceCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TravelOrderController extends Controller
{
    protected $RequestService;
    public function __construct(TravelOrderService $RequestService)
    {
        $this->RequestService = $RequestService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(AllTravelRequest $request)
    {
        $validatedData = $request->validated();
        $data = TravelOrder::when($request->has('employee_id'), function($query) use ($validatedData) {
            return $query->whereHas('employees', function($query2) use ($validatedData) {
                $query2->where('employee_id', $validatedData["employee_id"]);
            });
        })
        ->with("employees")
        ->orderBy("created_at", "DESC");

        return response()->json(dd($data->getBindings()));

        return new JsonResponse([
            'success' => true,
            'message' => 'Travel Order Request fetched.',
            'data' => PaginateResourceCollection::paginate(collect(TravelOrderResource::collection($data)))
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTravelOrderRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $main = new TravelOrder();
                $validdata = $request->validated();
                $main->fill($validdata);
                $main->request_status = StringRequestApprovalStatus::PENDING;
                $main->requested_by = auth()->user()->id;
                $main->save();
                $main->employees()->attach($validdata["employee_ids"]);
                $main->refresh();
                if ($main->getNextPendingApproval()) {
                    Users::find($main->getNextPendingApproval()['user_id'])->notify(new TravelRequestForApproval($main));
                }
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
        $main = TravelOrder::with(['department',"employees"])->find($id);
        $data = json_decode('{}');
        if (!is_null($main)) {
            $data->message = "Successfully fetch.";
            $data->success = true;
            $data->data = new TravelOrderResource($main);
            return response()->json($data);
        }
        $data->message = "No data found.";
        $data->success = false;
        return response()->json($data, 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTravelOrderRequest $request, $id)
    {
        $main = TravelOrder::find($id);
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
        $main = TravelOrder::find($id);
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

    public function myRequests(TravelRequest $request)
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
            'message' => 'TravelOrder Request fetched.',
            'data' => TravelOrderResource::collection($myRequest)
        ]);
    }

    /**
     * Show can view all pan request to be approved by logged in user (same login in manpower request)
     */
    public function myApprovals(TravelApprovalRequest $request)
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
            'message' => 'LeaveForm Request fetched.',
            'data' => TravelOrderResource::collection($myApproval)
        ]);
    }
}

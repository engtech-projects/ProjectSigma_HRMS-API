<?php

namespace App\Http\Controllers;

use App\Enums\StringRequestApprovalStatus;
use App\Models\TravelOrder;
use App\Http\Requests\StoreTravelOrderRequest;
use App\Http\Requests\UpdateTravelOrderRequest;
use App\Http\Resources\TravelOrderResource;
use App\Http\Services\TravelOrderService;
use App\Models\TravelOrderMembers;
use App\Models\Users;
use App\Notifications\TravelRequestForApproval;
use App\Utils\PaginateResourceCollection;
use Illuminate\Http\JsonResponse;
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
    public function index()
    {
        $main = $this->RequestService->getAll();
        $paginated = TravelOrderResource::collection($main);
        return new JsonResponse([
            'success' => true,
            'message' => 'TravelOrder Request fetched.',
            'data' => PaginateResourceCollection::paginate(collect($paginated), 15)
        ]);

        $main = TravelOrder::paginate(15);
        $data = json_decode('{}');
        $data->message = "Successfully fetch.";
        $data->success = true;
        $data->data = $main;
        return response()->json($data);
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
        $main = TravelOrder::find($id);
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
            'message' => 'TravelOrder Request fetched.',
            'data' => TravelOrderResource::collection($myRequest)
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
            'message' => 'LeaveForm Request fetched.',
            'data' => TravelOrderResource::collection($myApproval)
        ]);
    }
}

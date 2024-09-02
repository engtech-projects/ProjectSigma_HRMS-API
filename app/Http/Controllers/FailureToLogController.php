<?php

namespace App\Http\Controllers;

use App\Http\Requests\FailToLogRequest;
use App\Models\FailureToLog;
use Illuminate\Http\JsonResponse;
use App\Utils\PaginateResourceCollection;
use App\Http\Services\FailureToLogService;
use App\Http\Resources\FailureToLogResource;
use App\Exceptions\TransactionFailedException;
use App\Http\Requests\StoreFailureToLogRequest;
use App\Http\Requests\UpdateFailureToLogRequest;
use App\Models\Users;
use App\Notifications\FailureToLogRequestForApproval;

class FailureToLogController extends Controller
{
    protected $failedLogService;

    public function __construct(FailureToLogService $failedLogService)
    {
        $this->failedLogService = $failedLogService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(FailToLogRequest $request)
    {
        $validatedData = $request->validated();
        $data = FailureToLog::when($request->has('employee_id'), function($query) use ($validatedData) {
            return $query->whereHas('employee', function($query2) use ($validatedData) {
                $query2->where('employee_id', $validatedData["employee_id"]);
            });
        })
        ->with("employee")
        ->orderBy("created_at", "DESC")
        ->get();

        return new JsonResponse([
            'success' => true,
            'message' => 'Failure to Log Request fetched.',
            'data' => PaginateResourceCollection::paginate(collect(FailureToLogResource::collection($data)))
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFailureToLogRequest $request)
    {
        try {
            $valid = $request->validated();
            if($valid) {
                $main = FailureToLog::create($request->validated());
                $main->refresh();
                if ($main->getNextPendingApproval()) {
                    Users::find($main->getNextPendingApproval()['user_id'])->notify(new FailureToLogRequestForApproval($main));
                }
            }
        } catch (\Exception $e) {
            throw new TransactionFailedException("Transaction failed.", 500, $e);
        }

        return new JsonResponse([
            "success" => true,
            "message" => "Successfully created.",
        ], JsonResponse::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(FailureToLog $failedLog)
    {
        return new JsonResponse([
            "success" => true,
            "message" => "Successfully fetch.",
            "data" => new FailureToLogResource($failedLog->load('employee')),
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFailureToLogRequest $request, FailureToLog $failedLog)
    {
        try {
            $failedLog->update($request->validated());
        } catch (\Exception $e) {
            throw new TransactionFailedException("Transaction failed.", 500, $e);
        }
        return new JsonResponse([
            "success" => true,
            "message" => "Successfully updated.",
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FailureToLog $failedLogs)
    {
        try {
            $failedLogs->delete();
        } catch (\Exception $e) {
            throw new TransactionFailedException("Transaction failed.", 500, $e);
        }
        return new JsonResponse([
            "success" => true,
            "message" => "Successfully deleted.",
        ], JsonResponse::HTTP_OK);
    }

    public function myRequests()
    {
        $failedLog = $this->failedLogService->getMyRequests();
        $collection = collect(FailureToLogResource::collection($failedLog));

        return new JsonResponse([
            "success" => true,
            "message" => "Successfully fetch.",
            "data" => PaginateResourceCollection::paginate(collect($collection))
        ], JsonResponse::HTTP_OK);
    }
    public function myApprovals()
    {
        $failedLog = $this->failedLogService->getMyApprovals();
        $collection = collect(FailureToLogResource::collection($failedLog));

        return new JsonResponse([
            "success" => true,
            "message" => "Successfully fetch.",
            "data" => PaginateResourceCollection::paginate(collect($collection))
        ], JsonResponse::HTTP_OK);
    }
}

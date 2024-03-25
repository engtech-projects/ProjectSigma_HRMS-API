<?php

namespace App\Http\Controllers;

use App\Models\FailureToLog;
use Illuminate\Http\JsonResponse;
use App\Utils\PaginateResourceCollection;
use App\Http\Services\FailureToLogService;
use App\Http\Resources\FailureToLogResource;
use App\Http\Requests\StoreFailureToLogRequest;
use App\Http\Requests\UpdateFailureToLogRequest;

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
    public function index()
    {
        $failedLog = $this->failedLogService->getAll();
        $collection = collect(FailureToLogResource::collection($failedLog));

        return new JsonResponse([
            "success" => true,
            "message" => "Successfully fetch.",
            "data" => PaginateResourceCollection::paginate(collect($collection), 15)
        ], JsonResponse::HTTP_OK);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFailureToLogRequest $request)
    {
        $this->failedLogService->create($request->validated());

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
            "data" => new FailureToLogResource($failedLog),
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFailureToLogRequest $request, FailureToLog $failedLog)
    {
        $this->failedLogService->update($request->validated(), $failedLog);

        return new JsonResponse([
            "success" => true,
            "message" => "Successfully updated.",
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FailureToLog $failedLog)
    {
        $this->failedLogService->delete($failedLog);

        return new JsonResponse([
            "success" => true,
            "message" => "Successfully deleted.",
        ], JsonResponse::HTTP_OK);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ManpowerRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Services\ManpowerServices;
use Illuminate\Support\Facades\Storage;
use App\Utils\PaginateResourceCollection;
use App\Exceptions\TransactionFailedException;
use App\Http\Resources\ManpowerRequestResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Requests\StoreManpowerRequestRequest;
use App\Http\Requests\UpdateManpowerRequestRequest;

class ManpowerRequestController extends Controller
{
    protected $manpowerService;
    protected $manpowerRequestType = null;

    public function __construct(ManpowerServices $manpowerService)
    {
        $this->manpowerRequestType = request()->get('type');
        $this->manpowerService = $manpowerService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $manpowerRequests = $this->manpowerService->getAll();
        $collection = collect(ManpowerRequestResource::collection($manpowerRequests->load('user.employee')));

        if ($collection->isEmpty()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'No data found.',
            ], JsonResponse::HTTP_OK);
        }
        return new JsonResponse([
            'success' => true,
            'message' => 'Manpower Request fetched.',
            'data' => new JsonResource(PaginateResourceCollection::paginate($collection, 10))
        ]);
    }
    /**
     * Show List Manpower requests that have status “For Hiring“ = Approve
     */
    public function forHiring()
    {
        $manpowerRequest = $this->manpowerService->getAll();
        $manpowerRequest->load(['job_applicants', 'user.employee']);
        if ($manpowerRequest->isEmpty()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'No data found.',
            ], JsonResponse::HTTP_OK);
        }
        return new JsonResponse([
            'success' => true,
            'message' => 'Manpower Request fetched.',
            'data' => ManpowerRequestResource::collection($manpowerRequest->load('user.employee'))
        ]);
    }


    public function myRequest()
    {
        $myRequest = $this->manpowerService->getMyRequest();
        if ($myRequest->isEmpty()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'No data found.',
            ], JsonResponse::HTTP_OK);
        }
        return new JsonResponse([
            'success' => true,
            'message' => 'Manpower Request fetched.',
            'data' => ManpowerRequestResource::collection($myRequest)
        ]);
    }

    /**
     * Show all requests to be approved/reviewed by current user
     */
    public function myApproval()
    {
        $myApproval = $this->manpowerService->getMyApprovals();
        if ($myApproval->isEmpty()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'No data found.',
            ], JsonResponse::HTTP_OK);
        }
        return new JsonResponse([
            'success' => true,
            'message' => 'Manpower Request fetched.',
            'data' => ManpowerRequestResource::collection($myApproval)
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreManpowerRequestRequest $request)
    {
        $attributes = $request->validated();
        try {
            $this->manpowerService->createManpowerRequest($attributes);
        } catch (\Exception $e) {
            throw new TransactionFailedException("Create transaction failed.", 400, $e);
        }

        return new JsonResponse([
            "success" => true,
            "message" => "Successfully created.",
        ], JsonResponse::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(ManpowerRequest $manpowerRequest)
    {
        $newManpowerRequest = $manpowerRequest->load('user.employee');
        return new JsonResponse([
            'success' => true,
            'message' => 'Manpower request fetched.',
            'data' => new ManpowerRequestResource($newManpowerRequest)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateManpowerRequestRequest $request, ManpowerRequest $manpowerRequest)
    {
        try {
            $this->manpowerService->update($request->validated(), $manpowerRequest);
        } catch (\Exception $e) {
            throw new TransactionFailedException("Update transaction failed.", 400, $e);
        }
        return new JsonResponse([
            "success" => true,
            "message" => "Successfully updated."
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ManpowerRequest $manpowerRequest)
    {
        try {
            DB::transaction(function () use ($manpowerRequest) {
                return $manpowerRequest->delete();
            });
        } catch (\Exception $e) {
            throw new TransactionFailedException("Delete transaction failed.", 400, $e);
        }
        return new JsonResponse([
            "success" => true,
            "message" => "Successfully delete."
        ], JsonResponse::HTTP_OK);
    }
}

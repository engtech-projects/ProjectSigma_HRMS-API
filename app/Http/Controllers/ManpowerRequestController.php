<?php

namespace App\Http\Controllers;

use App\Models\ManpowerRequest;
use App\Models\ManpowerRequestJobApplicants;
use App\Models\JobApplicants;
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
use App\Http\Requests\StoreApplicantRequest;
use App\Enums\HiringStatuses;
use App\Enums\ManpowerRequestStatus;
use App\Http\Requests\ApprovedPositionsFilter;

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
        $collection = ManpowerRequestResource::collection($manpowerRequests)->response()->getData(true);
        if (empty($collection['data'])) {
            return new JsonResponse([
                'success' => false,
                'message' => 'No data found.',
            ], JsonResponse::HTTP_OK);
        }
        return new JsonResponse([
            'success' => true,
            'message' => 'Manpower Request fetched.',
            'data' => $collection
        ]);
    }

    public function openPositions()
    {
        $data = $this->manpowerService->getOpenPositions();
        $collection = ManpowerRequestResource::collection($data)->response()->getData(true);

        if (empty($collection['data'])) {
            return new JsonResponse([
                'success' => false,
                'message' => 'No data found.',
            ], JsonResponse::HTTP_OK);
        }

        return new JsonResponse([
            'success' => true,
            'message' => 'Manpower Request fetched.',
            'data' => $collection
        ]);
    }

    public function approvedPositions(ApprovedPositionsFilter $request)
    {
        $validatedData = $request->validated();
        $data = $this->manpowerService->getApprovedPositions($validatedData);
        $collection = ManpowerRequestResource::collection($data)->response()->getData(true);

        if (empty($collection['data'])) {
            return new JsonResponse([
                'success' => false,
                'message' => 'No data found.',
            ], JsonResponse::HTTP_OK);
        }

        return new JsonResponse([
            'success' => true,
            'message' => 'Manpower Request fetched.',
            'data' => $collection
        ]);
    }

    /**
     * Show List Manpower requests that have status “For Hiring“ = Approve
     */
    public function forHiring()
    {
        $manpowerRequest = $this->manpowerService->getAllForHiring();
        $collection = ManpowerRequestResource::collection($manpowerRequest)->response()->getData(true);

        if (empty($collection['data'])) {
            return new JsonResponse([
                'success' => false,
                'message' => 'No data found.',
                'data' => []
            ], JsonResponse::HTTP_OK);
        }

        return new JsonResponse([
            'success' => true,
            'message' => 'Manpower Request fetched.',
            'data' => $collection
        ]);
    }


    public function myRequest()
    {
        $myRequest = $this->manpowerService->getMyRequest();
        $collection = ManpowerRequestResource::collection($myRequest)->response()->getData(true);

        if (empty($collection['data'])) {
            return new JsonResponse([
                'success' => false,
                'message' => 'No data found.',
                'data' => []
            ], JsonResponse::HTTP_OK);
        }

        return new JsonResponse([
            'success' => true,
            'message' => 'Manpower Request fetched.',
            'data' => $collection
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Show all requests to be approved/reviewed by current user
     */
    public function myApproval()
    {
        $myApproval = $this->manpowerService->getMyApprovals();
        $collection = ManpowerRequestResource::collection($myApproval)->response()->getData(true);

        if (empty($collection['data'])) {
            return new JsonResponse([
                'success' => false,
                'message' => 'No data found.',
                'data' => []
            ], JsonResponse::HTTP_OK);
        }

        return new JsonResponse([
            'success' => true,
            'message' => 'Manpower Request fetched.',
            'data' => $collection
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreManpowerRequestRequest $request)
    {
        $attributes = $request->validated();
        $attributes["created_by"] = auth()->user()->id;
        $attributes["request_status"] = ManpowerRequestStatus::PENDING;
        try {
            $checkSave = $this->manpowerService->createManpowerRequest($attributes);
            if ($checkSave) {
                return new JsonResponse([
                    "success" => true,
                    "message" => "Successfully created.",
                ], JsonResponse::HTTP_CREATED);
            }
            return new JsonResponse([
                "success" => false,
                "message" => "Failed to save.",
            ], JsonResponse::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return new JsonResponse([
                "success" => false,
                "message" => "Failed to save.",
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

    }

    public function storeApplicant(StoreApplicantRequest $request)
    {
        $valid = $request->validated();
        $dbTransactionFailed = false;
        if ($valid) {
            $valid["processing_checklist"] = [
                "Interviewed" => false,
                "Tested" => false,
                "Reference_Checked" => false,
                "Medical_Examination" => false,
                "Contact_Extended" => false,
                "Contract_Signed" => false,
            ];
            $valid["hiring_status"] = HiringStatuses::PROCESSING->value;
            try {
                DB::transaction(function() use ($valid) {
                    foreach ($valid["data"] as $data) {
                        $valid["job_applicants_id"] = $data;
                        $record = ManpowerRequestJobApplicants::where('job_applicants_id', $valid["job_applicants_id"])
                        ->where('manpowerrequests_id', $valid["manpowerrequests_id"])->where("hiring_status", "Rejected")->first();

                        if (!$record) {
                            $model = new ManpowerRequestJobApplicants();
                            $model->fill($valid);
                            $model->save();
                        }

                        $model = JobApplicants::find($valid["job_applicants_id"]);
                        $model->status = "Processing";
                        $model->save();

                    }
                });
                return new JsonResponse([
                    "success" => true,
                    "message" => "Successfully save.",
                ], JsonResponse::HTTP_OK);
            } catch (Exception $e) {
                return new JsonResponse([
                    "success" => true,
                    "message" => "Failed to save.",
                ], JsonResponse::HTTP_OK);
            }
        }
        return new JsonResponse([
            "success" => true,
            "message" => "Failed to save.",
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Display the specified resource.
     */
    public function show(ManpowerRequest $resource)
    {
        $newManpowerRequest = $resource->load('user.employee');
        return new JsonResponse([
            'success' => true,
            'message' => 'Manpower request fetched.',
            'data' => new ManpowerRequestResource($newManpowerRequest)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateManpowerRequestRequest $request, $id)
    {
        try {
            $main = ManpowerRequest::find($id);
            if (is_null($main)) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'No data found.',
                ], 404);
            }
            $data = $this->manpowerService->update($request->validated(), $main);
            if ($data) {
                return new JsonResponse([
                    'success' => true,
                    'message' => 'Successfully save.',
                    'data' => $main,
                ], JsonResponse::HTTP_OK);
            }
            return new JsonResponse([
                'success' => false,
                'message' => 'Update failed.',
            ], 400);
        } catch (\Throwable $th) {
            return new JsonResponse([
                'success' => false,
                'error' => $th->getMessage(),
                'message' => 'Update transaction failed.',
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ManpowerRequest $resource)
    {
        try {
            DB::transaction(function () use ($resource) {
                return $resource->delete();
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

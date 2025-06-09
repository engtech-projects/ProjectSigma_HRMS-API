<?php

namespace App\Http\Controllers;

use App\Enums\RequestStatuses;
use App\Http\Requests\Request13thMonthDraft;
use App\Models\Request13thMonth;
use App\Http\Requests\StoreRequest13thMonthRequest;
use App\Http\Resources\Request13thMonthDetailedResource;
use App\Http\Resources\Request13thMonthListingResource;
use App\Http\Services\Payroll\Payroll13thMonthService;
use App\Models\Request13thMonthDetailAmounts;
use App\Models\Request13thMonthDetails;
use App\Models\Users;
use App\Notifications\Request13thMonthForApproval;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Request13thMonthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(
            [
                'data' => Request13thMonthListingResource::collection(Request13thMonth::paginate(10))->response()->getData(true),
                'success' => true,
                'message' => 'Successfully fetched 13th month requests',
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest13thMonthRequest $request)
    {
        // stores data generated from generateDraft
        $generatedRequest = null;
        $validatedData = $request->validated();
        $validatedData["created_by"] = auth()->user()->id;
        $validatedData["request_status"] = RequestStatuses::PENDING->value;
        DB::transaction(function () use (&$generatedRequest, $validatedData) {
            $generatedRequest = Request13thMonth::create(
                [
                    "date_requested" => $validatedData["date_requested"],
                    "date_from" => $validatedData["date_from"],
                    "date_to" => $validatedData["date_to"],
                    "employees" => $validatedData["employees"],
                    "days_advance" => $validatedData["days_advance"],
                    "charging_type" => $validatedData["charging_type"] ?? null,
                    "charging_id" => $validatedData["charging_id"] ?? null,
                    "metadata" => $validatedData["metadata"],
                    "approvals" => $validatedData["approvals"],
                    "request_status" => $validatedData["request_status"],
                    "created_by" => $validatedData["created_by"],
                ]
            );
            foreach ($validatedData["details"] as $detail) {
                $generatedDetail = $generatedRequest->details()->create([
                    "employee_id" => $detail["employee_id"],
                    "metadata" => json_encode($detail["metadata"]),
                ]);
                foreach ($detail["amounts"] as $amount) {
                    $generatedDetail->amounts()->create($amount);
                }
            }
            $generatedRequest->load("details.amounts");
            $generatedRequest->refresh();
            if ($generatedRequest->getNextPendingApproval()) {
                Users::find($generatedRequest->getNextPendingApproval()['user_id'])->notify(new Request13thMonthForApproval($generatedRequest));
            }
        });
        return response()->json(
            [
                'data' => $generatedRequest,
                'success' => true,
                'message' => 'Successfully created 13th month request',
            ]
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Request13thMonth $resource)
    {
        $resource->load("details.amounts");
        return response()->json(
            [
                'data' => new Request13thMonthDetailedResource($resource),
                'success' => true,
                'message' => 'Successfully fetched 13th month request',
            ]
        );
    }

    /**
     * Generate a draft for the 13th month payroll.
     * This method is used to generate a draft request for the 13th month.
     * It does not store the draft in the database, but prepares the data for submission.
     */
    public function generateDraft(Request13thMonthDraft $request)
    {
        $validatedData = $request->validated();
        $payrollService = new Payroll13thMonthService();
        try {
            $draftData = $payrollService->generateDraft(
                $validatedData["employee_ids"],
                $validatedData["date_from"],
                $validatedData["date_to"],
                $validatedData["days_advance"],
                $validatedData["charging_type"],
                $validatedData["charging_id"],
            );
        } catch (Exception $e) {
            Log::error('Failed to generate 13th month draft: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate draft data',
            ], 500);
        }
        $responseData = [
            'date_requested' => $validatedData["date_requested"],
            'date_from' => $validatedData["date_from"],
            'date_to' => $validatedData["date_to"],
            'employees' => $validatedData["employee_ids"],
            'days_advance' => $validatedData["days_advance"],
            'charging_type' => $validatedData["charging_type"] ?? null,
            'charging_id' => $validatedData["charging_id"] ?? null,
            'details' => $draftData['details'],
            'metadata' => $draftData['metadata'], // Assuming metadata is not required for draft generation
        ];
        $jsonData = $responseData;
        $modelData = new Request13thMonth($responseData);
        foreach ($draftData['details'] as $detail) {
            $childDetail = new Request13thMonthDetails($detail);
            foreach ($detail['amounts'] as $amount) {
                $childDetail->amounts->add(new Request13thMonthDetailAmounts($amount));
            }
            $modelData->details->add($childDetail);
        }
        return response()->json(
            [
                'data' => [
                    "json" => $jsonData,
                    "model" => new Request13thMonthDetailedResource($modelData),
                ],
                'success' => true,
                'message' => 'Successfully generated draft for 13th month request',
            ]
        );
    }

    public function myRequests()
    {
        $requests = Request13thMonth::myRequests()
        ->orderBy("created_at", "DESC")
        ->paginate(10);

        return response()->json(
            [
                'data' => Request13thMonthListingResource::collection($requests)->response()->getData(true),
                'success' => true,
                'message' => 'Successfully fetched my 13th month requests',
            ]
        );
    }

    public function myApprovals()
    {
        $requests = Request13thMonth::myApprovals()
        ->orderBy("created_at", "DESC")
        ->paginate(10);

        return response()->json(
            [
                'data' => Request13thMonthListingResource::collection($requests)->response()->getData(true),
                'success' => true,
                'message' => 'Successfully fetched my 13th month approvals',
            ]
        );
    }

}

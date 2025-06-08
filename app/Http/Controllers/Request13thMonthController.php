<?php

namespace App\Http\Controllers;

use App\Enums\RequestStatuses;
use App\Http\Requests\Request13thMonthDraft;
use App\Models\Request13thMonth;
use App\Http\Requests\StoreRequest13thMonthRequest;
use App\Http\Resources\Request13thMonthDetailedResource;
use App\Http\Resources\Request13thMonthListingResource;
use App\Http\Services\Payroll\Payroll13thMonthService;
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
        });

        return response()->json(
            [
                'data' => $generatedRequest->load("details.amounts"),
                'success' => true,
                'message' => 'Successfully created 13th month request',
            ]
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Request13thMonth $request13thMonth)
    {
        $request13thMonth->load("details.amounts");
        return response()->json(
            [
                'data' => new Request13thMonthDetailedResource($request13thMonth),
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
        return response()->json(
            [
                'data' => $responseData,
                'success' => true,
                'message' => 'Successfully generated draft for 13th month request',
            ]
        );
    }

}

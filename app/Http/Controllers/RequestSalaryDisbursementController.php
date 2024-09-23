<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateRequestSalaryDisbursementRequest;
use App\Models\RequestSalaryDisbursement;
use App\Http\Requests\StoreRequestSalaryDisbursementRequest;
use App\Http\Requests\UpdateRequestSalaryDisbursementRequest;
use App\Http\Resources\PayrollRecordsPayrollSummaryResource;
use App\Http\Resources\RequestPayrollSummaryResource;
use App\Models\PayrollDetail;
use App\Models\PayrollRecord;
use Illuminate\Http\JsonResponse;

class RequestSalaryDisbursementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $allRequests = RequestSalaryDisbursement::orderBy("created_at", "DESC")
        ->get();
        if ($allRequests->isEmpty()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'No data found.',
            ], JsonResponse::HTTP_OK);
        }
        return new JsonResponse([
            'success' => true,
            'message' => 'Cash Advance Request fetched.',
            'data' => RequestPayrollSummaryResource::collection($allRequests)
        ]);
    }

    public function generateDraft(GenerateRequestSalaryDisbursementRequest $request)
    {
        $validData = $request->validated();
        $generatedData = $validData;
        $payrollRecords = PayrollRecord::where([
            "payroll_date" => $validData["payroll_date"],
            "payroll_type" => $validData["payroll_type"],
            "release_type" => $validData["release_type"],
        ])->get();
        $payrollRecordIds = $payrollRecords->pluck("id");
        $payrollDetails = PayrollDetail::whereIn("payroll_record_id", $payrollRecordIds)
        ->with(['payroll_record'])
        ->orderBy("created_at", "DESC")
        ->get()
        ->append([
            'total_basic_pays',
            'total_overtime_pays',
            'total_cash_advance_payments',
            'total_loan_payments',
            'total_other_deduction_payments',
        ])
        ->sortBy('employee.fullname_first', SORT_NATURAL)
        ->values();
        $uniqueGroup =  $payrollDetails->groupBy('payroll_record.charging_name');
        $resourceFormattedData = PayrollRecordsPayrollSummaryResource::collection($uniqueGroup);
        $generatedData["payroll_records_ids"] = $payrollRecordIds;
        $generatedData["summary"] = $resourceFormattedData;
        return new JsonResponse([
            'success' => true,
            'message' => 'Employee Remittance Request fetched.',
            'data' => $generatedData,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequestSalaryDisbursementRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(RequestSalaryDisbursement $requestSalaryDisbursement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequestSalaryDisbursementRequest $request, RequestSalaryDisbursement $requestSalaryDisbursement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RequestSalaryDisbursement $requestSalaryDisbursement)
    {

    }

    public function myRequests()
    {
        $myRequests = RequestSalaryDisbursement::myRequests()
        ->orderBy("created_at", "DESC")
        ->get();
        if ($myRequests->isEmpty()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'No data found.',
            ], JsonResponse::HTTP_OK);
        }
        return new JsonResponse([
            'success' => true,
            'message' => 'Leave Request fetched.',
            'data' => RequestPayrollSummaryResource::collection($myRequests)
        ]);
    }

    /**
     * Show can view all pan request to be approved by logged in user (same login in manpower request)
     */
    public function myApprovals()
    {
        $myApproval = RequestSalaryDisbursement::myApprovals()
        ->orderBy("created_at", "DESC")
        ->get();
        if ($myApproval->isEmpty()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'No data found.',
            ], JsonResponse::HTTP_OK);
        }
        return new JsonResponse([
            'success' => true,
            'message' => 'Cash Advance Request fetched.',
            'data' => RequestPayrollSummaryResource::collection($myApproval)
        ]);
    }
}

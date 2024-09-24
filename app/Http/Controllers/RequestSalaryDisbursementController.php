<?php

namespace App\Http\Controllers;

use App\Enums\RequestStatuses;
use App\Http\Requests\GenerateRequestSalaryDisbursementRequest;
use App\Models\RequestSalaryDisbursement;
use App\Http\Requests\StoreRequestSalaryDisbursementRequest;
use App\Http\Requests\UpdateRequestSalaryDisbursementRequest;
use App\Http\Resources\PayrollRecordsPayrollSummaryResource;
use App\Http\Resources\RequestPayrollSummaryResource;
use App\Models\PayrollDetail;
use App\Models\PayrollDetailsCharging;
use App\Models\PayrollRecord;
use App\Utils\PaginateResourceCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

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
            'data' => PaginateResourceCollection::paginate(RequestPayrollSummaryResource::collection($allRequests)->collect())
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
        ])
        ->isApproved()
        ->get();
        $payrollRecordIds = $payrollRecords->pluck("id");
        $payrollDetailsIds = PayrollDetail::whereIn("payroll_record_id", $payrollRecordIds)->get()->pluck("id");
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
        $chargings = PayrollDetailsCharging::whereIn("payroll_details_id", $payrollDetailsIds)->get()->append(["charging_name"]);
        $uniqueSalaries = $chargings->groupBy(['charging_name']);
        [
            "Salary Regular Regular",
            "Salary Rest Regular",
            "Salary RegularHoliday Regular",
            "Salary SpecialHoliday Regular",
            "Salary Adjustment",
        ];
        [
            "Salary Regular Overtime",
            "Salary Rest Overtime",
            "Salary RegularHoliday Overtime",
            "Salary SpecialHoliday Overtime",
        ];
        $resourceFormattedData = PayrollRecordsPayrollSummaryResource::collection($uniqueGroup);
        $generatedData["payroll_records_ids"] = $payrollRecordIds;
        $generatedData["summary"] = $resourceFormattedData;
        $generatedData["salaries"] = $uniqueSalaries;
        return new JsonResponse([
            'success' => true,
            'message' => 'Payroll Summary Created.',
            'data' => $generatedData,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequestSalaryDisbursementRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData["created_by"] = auth()->user()->id;
        $validatedData["request_status"] = RequestStatuses::PENDING;
        DB::transaction(function () use ($validatedData) {
            $createdRequest = RequestSalaryDisbursement::create($validatedData);
            $createdRequest->payroll_records()->attach($validatedData["payroll_records_ids"]);
        });
        return new JsonResponse([
            'success' => true,
            'message' => 'Request Success.',
        ]);
        // return new JsonResponse([
        //     'success' => false,
        //     'message' => 'Unknown Error Occured.',
        // ], JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * Display the specified resource.
     */
    public function show(RequestSalaryDisbursement $requestSalaryDisbursement)
    {
        return new JsonResponse([
            'success' => true,
            'message' => 'Resource Fetched.',
            'data' => new RequestPayrollSummaryResource($requestSalaryDisbursement),
        ]);
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
            'message' => 'Request fetched.',
            'data' => PaginateResourceCollection::paginate(RequestPayrollSummaryResource::collection($myRequests)->collect()),
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
            'message' => 'Request fetched.',
            'data' => PaginateResourceCollection::paginate(RequestPayrollSummaryResource::collection($myApproval)->collect()),
        ]);
    }
}

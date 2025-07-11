<?php

namespace App\Http\Controllers;

use App\Enums\DisbursementStatus;
use App\Enums\RequestStatuses;
use App\Http\Requests\GenerateRequestSalaryDisbursementRequest;
use App\Http\Requests\PayrollRecordsListFilterRequest;
use App\Models\RequestSalaryDisbursement;
use App\Http\Requests\StoreRequestSalaryDisbursementRequest;
use App\Http\Requests\UpdateRequestSalaryDisbursementRequest;
use App\Http\Resources\ApprovalAttributeResource;
use App\Http\Resources\PayrollRecordsPayrollSummaryResource;
use App\Http\Resources\PayslipReadyListResource;
use App\Http\Resources\RequestPayrollSummaryListResource;
use App\Http\Resources\RequestPayrollSummaryResource;
use App\Http\Services\ApiServices\AccountingSecretkeyService;
use App\Http\Services\Payroll\SalaryDisbursementService;
use App\Models\PayrollDetail;
use App\Models\PayrollRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class RequestSalaryDisbursementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(PayrollRecordsListFilterRequest $request)
    {
        $allRequests = RequestSalaryDisbursement::when($request->has("payroll_date") && $request->payroll_date != '', function ($query) use ($request) {
            return $query->whereDate("payroll_date", $request->payroll_date);
        })
        ->when($request->has("payroll_type") && $request->payroll_type != '', function ($query) use ($request) {
            $query->where("payroll_type", $request->payroll_type);
        })
        ->when($request->has("release_type") && $request->release_type != '', function ($query) use ($request) {
            $query->where("release_type", $request->release_type);
        })
        ->when($request->has("project_id") && $request->project_id != '', function ($query) use ($request) {
            $query->where("project_id", $request->project_id);
        })
        ->when($request->has("department_id") && $request->department_id != '', function ($query) use ($request) {
            $query->where("department_id", $request->department_id);
        })
        ->orderBy("created_at", "DESC")
        ->paginate(config("app.pagination_per_page"));
        return RequestPayrollSummaryListResource::collection($allRequests)
        ->additional([
            'success' => true,
            'message' => 'Cash Advance Request fetched.',
        ]);
    }

    public function generateDraft(GenerateRequestSalaryDisbursementRequest $request)
    {
        $validData = $request->validated();
        $generatedData = $validData;
        $payrollRecords = SalaryDisbursementService::getPayrollRecordsForDisbursement($validData["payroll_date"], $validData["payroll_type"], $validData["release_type"]);
        $payrollRecordIds = $payrollRecords->pluck("id");
        $payrollSummaryDatas = SalaryDisbursementService::getPayrollSummary($payrollRecordIds);
        $payrollSummaryResource = PayrollRecordsPayrollSummaryResource::collection($payrollSummaryDatas);
        $generatedData["payroll_records_ids"] = $payrollRecordIds;
        $generatedData["summary"] = $payrollSummaryResource;
        $generatedData["approvals"] = ApprovalAttributeResource::collection($validData["approvals"]);
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
        // CHECK FOR PENDING PAYROLL RECORDS
        $payrollRecords = PayrollRecord::whereIn("id", $validatedData["payroll_records_ids"])->isPending()->get();
        if (!$payrollRecords->isEmpty()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Cannot create request for pending payroll records.',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
        $validatedData["request_status"] = RequestStatuses::PENDING;
        $validatedData["created_by"] = auth()->user()->id;
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
    public function show(RequestSalaryDisbursement $resource)
    {
        return new JsonResponse([
            'success' => true,
            'message' => 'Resource Fetched.',
            'data' => new RequestPayrollSummaryResource($resource),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequestSalaryDisbursementRequest $request, RequestSalaryDisbursement $resource)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RequestSalaryDisbursement $resource)
    {

    }

    public function myRequests(PayrollRecordsListFilterRequest $request)
    {
        $myRequests = RequestSalaryDisbursement::when($request->has("payroll_date") && $request->payroll_date != '', function ($query) use ($request) {
            return $query->whereDate("payroll_date", $request->payroll_date);
        })
        ->when($request->has("payroll_type") && $request->payroll_type != '', function ($query) use ($request) {
            $query->where("payroll_type", $request->payroll_type);
        })
        ->when($request->has("release_type") && $request->release_type != '', function ($query) use ($request) {
            $query->where("release_type", $request->release_type);
        })
        ->when($request->has("project_id") && $request->project_id != '', function ($query) use ($request) {
            $query->where("project_id", $request->project_id);
        })
        ->when($request->has("department_id") && $request->department_id != '', function ($query) use ($request) {
            $query->where("department_id", $request->department_id);
        })
        ->myRequests()
        ->orderBy("created_at", "DESC")
        ->paginate(config("app.pagination_per_page"));
        return RequestPayrollSummaryListResource::collection($myRequests)
        ->additional([
            'success' => true,
            'message' => 'Cash Advance Request fetched.',
        ]);
    }

    /**
     * Show can view all pan request to be approved by logged in user (same login in manpower request)
     */
    public function myApprovals(PayrollRecordsListFilterRequest $request)
    {
        $myApproval = RequestSalaryDisbursement::when($request->has("payroll_date") && $request->payroll_date != '', function ($query) use ($request) {
            return $query->whereDate("payroll_date", $request->payroll_date);
        })
        ->when($request->has("payroll_type") && $request->payroll_type != '', function ($query) use ($request) {
            $query->where("payroll_type", $request->payroll_type);
        })
        ->when($request->has("release_type") && $request->release_type != '', function ($query) use ($request) {
            $query->where("release_type", $request->release_type);
        })
        ->when($request->has("project_id") && $request->project_id != '', function ($query) use ($request) {
            $query->where("project_id", $request->project_id);
        })
        ->when($request->has("department_id") && $request->department_id != '', function ($query) use ($request) {
            $query->where("department_id", $request->department_id);
        })
        ->myApprovals()
        ->orderBy("created_at", "DESC")
        ->paginate(config("app.pagination_per_page"));
        return RequestPayrollSummaryListResource::collection($myApproval)
        ->additional([
            'success' => true,
            'message' => 'Cash Advance Request fetched.',
        ]);
    }

    /**
     * All Salary Disbursement Requests that are Ready for Payslip
     */
    public function payslipReady()
    {
        $payslipReady = RequestSalaryDisbursement::isApproved()
        ->where("disbursement_status", DisbursementStatus::RELEASED)
        ->orderBy("created_at", "DESC")
        ->paginate(config("app.pagination_per_page"));
        return RequestPayrollSummaryListResource::collection($payslipReady)
        ->additional([
            'success' => true,
            'message' => 'Cash Advance Request fetched.',
        ]);
    }
    /**
     * All Salary Disbursement Requests that are Ready for Payslip
     */
    public function payslipReadyShow(RequestSalaryDisbursement $requestSalaryDisbursement)
    {
        $payrollDetails = PayrollDetail::whereIn("payroll_record_id", $requestSalaryDisbursement->payroll_records->pluck('id')->all())
        ->get()
        ->sortBy("employee.fullname_last", SORT_NATURAL)
        ->values()
        ->all();
        if (collect($payrollDetails)->isEmpty()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'No data found.',
            ], JsonResponse::HTTP_OK);
        }
        return new JsonResponse([
            'success' => true,
            'message' => 'Request fetched.',
            'data' => PayslipReadyListResource::collection($payrollDetails),
        ]);
    }

    public function submitToAccounting(RequestSalaryDisbursement $requestSalaryDisbursement)
    {
        $requestSalaryDisbursement->update([
            "disbursement_status" => DisbursementStatus::PROCESSING
        ]);
        $accountingService = new AccountingSecretkeyService();
        $submitResult = $accountingService->submitPayrollRequest($requestSalaryDisbursement);
        if (!$submitResult["success"]) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed to submit request to accounting.' . $submitResult["message"],
            ], JsonResponse::HTTP_NOT_ACCEPTABLE);
        }
        return new JsonResponse([
            'success' => true,
            'message' => 'Request submitted to accounting.',
        ]);
    }
}

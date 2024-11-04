<?php

namespace App\Http\Controllers;

use App\Enums\EmployeeLoanType;
use App\Http\Requests\HdmfEmployeeLoansRequest;
use App\Http\Requests\HdmfGroupSummaryLoansRequest;
use App\Http\Requests\PagibigEmployeeRemittanceRequest;
use App\Http\Requests\PagibigGroupRemittanceRequest;
use App\Http\Requests\PagibigRemittanceSummaryRequest;
use App\Http\Requests\PhilhealthEmployeeRemittanceRequest;
use App\Http\Requests\PhilhealthGroupRemittanceRequest;
use App\Http\Requests\PhilhealthRemittanceSummaryRequest;
use App\Http\Requests\SssEmployeeLoansRequest;
use App\Http\Requests\SssEmployeeRemittanceRequest;
use App\Http\Requests\SssGroupRemittanceRequest;
use App\Http\Requests\SssGroupSummaryLoansRequest;
use App\Http\Requests\sssRemittanceSummaryRequest;
use App\Http\Services\Report\ReportService;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    protected $reportService;
    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }
    public function sssEmployeeRemittanceGenerate(SssEmployeeRemittanceRequest $request)
    {
        return new JsonResponse($this->reportService->sssEmployeeRemittance($request->validated()));
    }
    public function sssGroupRemittanceGenerate(SssGroupRemittanceRequest $request)
    {
        return new JsonResponse($this->reportService->sssGroupRemittance($request->validated()));
    }
    public function sssRemittanceSummary(sssRemittanceSummaryRequest $request)
    {
        return new JsonResponse($this->reportService->sssRemittanceSummary($request->validated()));
    }
    public function pagibigEmployeeRemittanceGenerate(PagibigEmployeeRemittanceRequest $request)
    {
        return new JsonResponse($this->reportService->pagibigEmployeeRemittance($request->validated()));
    }
    public function pagibigGroupRemittanceGenerate(PagibigGroupRemittanceRequest $request)
    {
        return new JsonResponse($this->reportService->pagibigGroupRemiitance($request->validated()));
    }
    public function pagibigRemittanceSummary(PagibigRemittanceSummaryRequest $request)
    {
        return new JsonResponse($this->reportService->pagibigRemittanceSummary($request->validated()));
    }
    public function philhealthEmployeeRemittanceGenerate(PhilhealthEmployeeRemittanceRequest $request)
    {
        return new JsonResponse($this->reportService->philhealthEmployeeRemittance($request->validated()));
    }
    public function philhealthGroupRemittanceGenerate(PhilhealthGroupRemittanceRequest $request)
    {
        return new JsonResponse($this->reportService->philhealthGroupRemittance($request->validated()));
    }
    public function philhealthRemittanceSummary(PhilhealthRemittanceSummaryRequest $request)
    {
        return new JsonResponse($this->reportService->philhealthRemittanceSummary($request->validated()));
    }
    public function sssEmployeeLoans(SssEmployeeLoansRequest $request)
    {
        return new JsonResponse($this->reportService->sssEmployeeLoans($request->validated()));
    }
    public function hdmfEmployeeLoans(HdmfEmployeeLoansRequest $request)
    {
        return new JsonResponse($this->reportService->hdmfEmployeeLoans($request->validated()));
    }
    public function coopEmployeeLoans(HdmfEmployeeLoansRequest $request)
    {
        return new JsonResponse($this->reportService->coopEmployeeLoans($request->validated()));
    }
    public function coopGroupSummaryLoans(HdmfEmployeeLoansRequest $request)
    {
        return new JsonResponse($this->reportService->coopGroupSummaryLoans($request->validated()));
    }
    public function sssGroupSummaryLoans(SssGroupSummaryLoansRequest $request)
    {
        return new JsonResponse($this->reportService->sssGroupSummaryLoans($request->validated()));
    }
    public function hdmfGroupSummaryLoans(HdmfGroupSummaryLoansRequest $request)
    {
        return new JsonResponse($this->reportService->hdmfGroupSummaryLoans($request->validated()));
    }
}

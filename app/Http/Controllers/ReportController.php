<?php

namespace App\Http\Controllers;

use App\Enums\EmployeeLoanType;
use App\Http\Requests\DefaultPaymentRequest;
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
    public function sssEmployeeRemittanceGenerate(SssEmployeeRemittanceRequest $request)
    {
        return new JsonResponse(ReportService::sssEmployeeRemittance($request->validated()));
    }
    public function sssGroupRemittanceGenerate(SssGroupRemittanceRequest $request)
    {
        return new JsonResponse(ReportService::sssGroupRemittance($request->validated()));
    }
    public function sssRemittanceSummary(sssRemittanceSummaryRequest $request)
    {
        return new JsonResponse(ReportService::sssRemittanceSummary($request->validated()));
    }
    public function pagibigEmployeeRemittanceGenerate(PagibigEmployeeRemittanceRequest $request)
    {
        return new JsonResponse(ReportService::pagibigEmployeeRemittance($request->validated()));
    }
    public function pagibigGroupRemittanceGenerate(PagibigGroupRemittanceRequest $request)
    {
        return new JsonResponse(ReportService::pagibigGroupRemiitance($request->validated()));
    }
    public function pagibigRemittanceSummary(PagibigRemittanceSummaryRequest $request)
    {
        return new JsonResponse(ReportService::pagibigRemittanceSummary($request->validated()));
    }
    public function philhealthEmployeeRemittanceGenerate(PhilhealthEmployeeRemittanceRequest $request)
    {
        return new JsonResponse(ReportService::philhealthEmployeeRemittance($request->validated()));
    }
    public function philhealthGroupRemittanceGenerate(PhilhealthGroupRemittanceRequest $request)
    {
        return new JsonResponse(ReportService::philhealthGroupRemittance($request->validated()));
    }
    public function philhealthRemittanceSummary(PhilhealthRemittanceSummaryRequest $request)
    {
        return new JsonResponse(ReportService::philhealthRemittanceSummary($request->validated()));
    }
    public function sssEmployeeLoans(SssEmployeeLoansRequest $request)
    {
        return new JsonResponse(ReportService::sssEmployeeLoans($request->validated()));
    }
    public function hdmfEmployeeLoans(HdmfEmployeeLoansRequest $request)
    {
        return new JsonResponse(ReportService::hdmfEmployeeLoans($request->validated()));
    }
    public function coopEmployeeLoans(HdmfEmployeeLoansRequest $request)
    {
        return new JsonResponse(ReportService::coopEmployeeLoans($request->validated()));
    }
    public function coopGroupSummaryLoans(HdmfEmployeeLoansRequest $request)
    {
        return new JsonResponse(ReportService::coopGroupSummaryLoans($request->validated()));
    }
    public function sssGroupSummaryLoans(SssGroupSummaryLoansRequest $request)
    {
        return new JsonResponse(ReportService::sssGroupSummaryLoans($request->validated()));
    }
    public function hdmfGroupSummaryLoans(HdmfGroupSummaryLoansRequest $request)
    {
        return new JsonResponse(ReportService::hdmfGroupSummaryLoans($request->validated()));
    }
    public function getDefaultLoanPayments(DefaultPaymentRequest $request)
    {
        return new JsonResponse(ReportService::getDefaultLoanPayments($request->validated()));
    }
    public function getDefaultLoanPaymentsGroup(DefaultPaymentRequest $request)
    {
        return new JsonResponse(ReportService::getDefaultLoanPaymentsGroup($request->validated()));
    }
    public function hdmfCalamityGroupSummaryLoans(HdmfGroupSummaryLoansRequest $request)
    {
        return new JsonResponse(ReportService::hdmfCalamityGroupSummaryLoans($request->validated()));
    }
    public function hdmfCalamityEmployeeLoans(HdmfEmployeeLoansRequest $request)
    {
        return new JsonResponse(ReportService::hdmfCalamityEmployeeLoans($request->validated()));
    }
    public function getLoanCategoryList()
    {
        return new JsonResponse(ReportService::getLoanCategoryList());
    }
}

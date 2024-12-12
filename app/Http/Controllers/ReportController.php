<?php

namespace App\Http\Controllers;

use App\Enums\Reports\OtherDeductionReports;
use App\Http\Requests\DefaultPaymentRequest;
use App\Http\Requests\HdmfEmployeeLoansRequest;
use App\Http\Requests\HdmfGroupSummaryLoansRequest;
use App\Http\Requests\PagibigEmployeeRemittanceRequest;
use App\Http\Requests\PagibigGroupRemittanceRequest;
use App\Http\Requests\PagibigRemittanceSummaryRequest;
use App\Http\Requests\PhilhealthEmployeeRemittanceRequest;
use App\Http\Requests\PhilhealthGroupRemittanceRequest;
use App\Http\Requests\PhilhealthRemittanceSummaryRequest;
use App\Http\Requests\Reports\OtherDeductionPaymentsReportRequest;
use App\Http\Requests\SssEmployeeLoansRequest;
use App\Http\Requests\SssEmployeeRemittanceRequest;
use App\Http\Requests\SssGroupRemittanceRequest;
use App\Http\Requests\SssGroupSummaryLoansRequest;
use App\Http\Requests\sssRemittanceSummaryRequest;
use App\Http\Resources\Reports\OtherDeductionDefaultEmployee;
use App\Http\Resources\Reports\OtherDeductionDefaultSummary;
use App\Http\Resources\Reports\OtherDeductionMP2Employee;
use App\Http\Resources\Reports\OtherDeductionMP2Summary;
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
    public function hdmfCalamityGroupSummaryLoans(HdmfGroupSummaryLoansRequest $request)
    {
        return new JsonResponse(ReportService::hdmfCalamityGroupSummaryLoans($request->validated()));
    }
    public function hdmfCalamityEmployeeLoans(HdmfEmployeeLoansRequest $request)
    {
        return new JsonResponse(ReportService::hdmfCalamityEmployeeLoans($request->validated()));
    }
    // LOAN REPORTS
    public function loanCategoryList()
    {
        return new JsonResponse(ReportService::getLoanCategoryList());
    }
    public function loanDefaultEmployee(DefaultPaymentRequest $request)
    {
        return new JsonResponse(ReportService::getDefaultLoanPayments($request->validated()));
    }
    public function loanDefaultGroup(DefaultPaymentRequest $request)
    {
        return new JsonResponse(ReportService::getDefaultLoanPaymentsGroup($request->validated()));
    }
    public function loanSssEmployee(DefaultPaymentRequest $request)
    {
        return new JsonResponse(ReportService::getDefaultLoanPayments($request->validated()));
    }
    public function loanSssGroup(DefaultPaymentRequest $request)
    {
        return new JsonResponse(ReportService::getDefaultLoanPaymentsGroup($request->validated()));
    }
    public function loanCoopEmployee(DefaultPaymentRequest $request)
    {
        return new JsonResponse(ReportService::getDefaultLoanPayments($request->validated()));
    }
    public function loanCoopGroup(DefaultPaymentRequest $request)
    {
        return new JsonResponse(ReportService::getDefaultLoanPaymentsGroup($request->validated()));
    }
    public function loanHdmfEmployee(DefaultPaymentRequest $request)
    {
        return new JsonResponse(ReportService::getDefaultLoanPayments($request->validated()));
    }
    public function loanHdmfGroup(DefaultPaymentRequest $request)
    {
        return new JsonResponse(ReportService::getDefaultLoanPaymentsGroup($request->validated()));
    }
    public function loanHdmfCalamityEmployee(DefaultPaymentRequest $request)
    {
        return new JsonResponse(ReportService::getDefaultLoanPayments($request->validated()));
    }
    public function loanHdmfCalamityGroup(DefaultPaymentRequest $request)
    {
        return new JsonResponse(ReportService::getDefaultLoanPaymentsGroup($request->validated()));
    }
    // OTHER DEDUCTION REPORTS
    public function otherDeductionsCategoryList()
    {
        return new JsonResponse(ReportService::otherDeductionsCategoryList());
    }
    public function otherDeductionsDefaultEmployee(OtherDeductionPaymentsReportRequest $request)
    {
        $validated = $request->validated();
        $reportData = null;
        if ($validated['report_type'] == 'employee') {
            $reportData = ReportService::getOtherDeductionEmployeeReport($validated);
            if ($validated["loan_type"] == OtherDeductionReports::MP2->value) {
                $reportData = OtherDeductionMP2Employee::collection($reportData);
            } else {
                $reportData = OtherDeductionDefaultEmployee::collection($reportData);
            }
        } elseif ($validated['report_type'] == 'summary-with-group') {
            $reportData = ReportService::getOtherDeductionGroupReport($validated);
            if ($validated["loan_type"] == OtherDeductionReports::MP2->value) {
                $reportData = OtherDeductionMP2Summary::collection($reportData);
            } else {
                $reportData = OtherDeductionDefaultSummary::collection($reportData);
            }
        }
        return new JsonResponse($reportData);
    }

}

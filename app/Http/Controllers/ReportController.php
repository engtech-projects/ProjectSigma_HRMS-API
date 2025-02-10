<?php

namespace App\Http\Controllers;

use App\Enums\Reports\LoanReports;
use App\Enums\Reports\AdministrativeReport;
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
use App\Http\Requests\Reports\LoanPaymentsReportRequest;
use App\Http\Requests\Reports\OtherDeductionPaymentsReportRequest;
use App\Http\Requests\SssEmployeeLoansRequest;
use App\Http\Requests\SssEmployeeRemittanceRequest;
use App\Http\Requests\SssGroupRemittanceRequest;
use App\Http\Requests\SssGroupSummaryLoansRequest;
use App\Http\Requests\Reports\AdministrativeReportRequest;
use App\Http\Requests\sssRemittanceSummaryRequest;
use App\Http\Resources\Reports\LoanCalamityEmployee;
use App\Http\Resources\Reports\LoanCalamitySummary;
use App\Http\Resources\Reports\LoanCoopEmployee;
use App\Http\Resources\Reports\LoanCoopSummary;
use App\Http\Resources\Reports\LoanDefaultEmployee;
use App\Http\Resources\Reports\LoanDefaultSummary;
use App\Http\Resources\Reports\LoanMplEmployee;
use App\Http\Resources\Reports\LoanMplSummary;
use App\Http\Resources\Reports\LoanSssEmployee;
use App\Http\Resources\Reports\LoanSssSummary;
use App\Http\Resources\Reports\OtherDeductionDefaultEmployee;
use App\Http\Resources\Reports\OtherDeductionDefaultSummary;
use App\Http\Resources\Reports\OtherDeductionMP2Employee;
use App\Http\Resources\Reports\OtherDeductionMP2Summary;
use App\Http\Services\Report\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Spatie\SimpleExcel\SimpleExcelReader;
use Illuminate\Support\Facades\Storage;

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
    /*
    * LOAN REPORTS
    */
    public function loanCategoryList()
    {
        return new JsonResponse(ReportService::getLoanCategoryList());
    }
    public function loanReportsGenerate(LoanPaymentsReportRequest $request)
    {
        $validated = $request->validated();
        $reportData = null;
        if ($validated['report_type'] == 'employee') {
            $reportData = ReportService::getLoanEmployeeReport($validated);
            switch ($validated["loan_type"]) {
                case LoanReports::HDMF_MPL->value:
                case LoanReports::HDMF_MPL_LOAN->value:
                    $reportData = LoanMplEmployee::collection($reportData);
                    break;
                case LoanReports::COOP->value:
                    $reportData = LoanCoopEmployee::collection($reportData);
                    break;
                case LoanReports::SSS->value:
                    $reportData = LoanSssEmployee::collection($reportData);
                    break;
                case LoanReports::HDMF_CALAMITY_LOAN->value:
                case LoanReports::CALAMITY_LOAN->value:
                    $reportData = LoanCalamityEmployee::collection($reportData);
                    break;
                default:
                    $reportData = LoanDefaultEmployee::collection($reportData);
                    break;
            }
        } elseif ($validated['report_type'] == 'summary-with-group') {
            $reportDataGroup = ReportService::getLoanGroupReport($validated);
            switch ($validated["loan_type"]) {
                case LoanReports::HDMF_MPL->value:
                case LoanReports::HDMF_MPL_LOAN->value:
                    $reportData = LoanMplSummary::collection($reportDataGroup);
                    break;
                case LoanReports::COOP->value:
                    $reportData = LoanCoopSummary::collection($reportDataGroup);
                    break;
                case LoanReports::SSS->value:
                    $reportData = LoanSssSummary::collection($reportDataGroup);
                    break;
                case LoanReports::HDMF_CALAMITY_LOAN->value:
                case LoanReports::CALAMITY_LOAN->value:
                    $reportData = LoanCalamitySummary::collection($reportDataGroup);
                    break;
                default:
                    $reportData = LoanDefaultSummary::collection($reportDataGroup);
                    break;
            }
        }
        return new JsonResponse([
            "success" => true,
            "message" => "Successfully fetched.",
            "data" => $reportData
        ]);
    }
    /*
    * OTHER DEDUCTION REPORTS
    */
    public function otherDeductionsCategoryList()
    {
        return new JsonResponse(ReportService::otherDeductionsCategoryList());
    }
    public function otherDeductionsReports(OtherDeductionPaymentsReportRequest $request)
    {
        $validated = $request->validated();
        $reportData = null;
        if ($validated['report_type'] == 'employee') {
            $reportData = ReportService::getOtherDeductionEmployeeReport($validated);
            switch ($validated["loan_type"]) {
                case OtherDeductionReports::MP2->value:
                    $reportData = OtherDeductionMP2Employee::collection($reportData);
                    break;
                default:
                    $reportData = OtherDeductionDefaultEmployee::collection($reportData);
                    break;
            }
        } elseif ($validated['report_type'] == 'summary-with-group') {
            $reportData = ReportService::getOtherDeductionGroupReport($validated);
            switch ($validated["loan_type"]) {
                case OtherDeductionReports::MP2->value:
                    $reportData = OtherDeductionMP2Summary::collection($reportData);
                    break;
                default:
                    $reportData = OtherDeductionDefaultSummary::collection($reportData);
                    break;
            }
        }
        return new JsonResponse([
            "success" => true,
            "message" => "Successfully fetched.",
            "data" => $reportData
        ]);
    }

    public function employeeTenureshipList(AdministrativeReportRequest $request)
    {
        $validate = $request->validated();
        if ($validate) {
            return new JsonResponse([
                "success" => true,
                'message' => 'Employee Tenureship List fetched successfully.',
                'data' => ReportService::employeeTenureshipList($validate)],
                JsonResponse::HTTP_OK
            );
        }
        return new JsonResponse([
            "success" => false,
            'message' => $e->getMessage()]
        );
    }

    public function administrativeReportsGenerate(AdministrativeReportRequest $request)
    {
        $validated = $request->validated();
        $reportData = null;
        if ($validated) {
            switch ($validated["report_type"]) {
                case AdministrativeReport::EMPLOYEE_MASTERLIST->value:
                    $reportData = ReportService::employeeMasterList($validated);
                    break;
                case AdministrativeReport::EMPLOYEE_TENURESHIP->value:
                    $reportData = ReportService::employeeTenureshipList($validated);
                    break;
                case AdministrativeReport::EMPLOYEE_NEWHIRE->value:
                    $reportData = ReportService::employeeNewList($validated);
                    break;
                case AdministrativeReport::EMPLOYEE_LEAVES->value:
                    $reportData = ReportService::employeeLeaves($validated);
                    break;
                case AdministrativeReport::EMPLOYEE_ABSENCES->value:
                    $reportData = ReportService::employeeAbsences($validated);
                    break;
            }
        }
        return new JsonResponse([
            "success" => true,
            "message" => "Successfully fetched.",
            "data" => $reportData
        ]);
    }

    public function administrativeExportReports(AdministrativeReportRequest $request)
    {
        $validated = $request->validated();
        $masterListHeaders = [
            'Employee ID',
            'Date Hired',
            'Last Name',
            'First Name',
            'Middle Name',
            'Suffix',
            'Nickname',
            'Present Address',
            'Permanent Address',
            'Cellphone',
            'Date of Birth',
            'Place of Birth',
            'Citizenship',
            'Blood Type', 'Gender',
            'Religion',
            'Civil Status',
            'Height',
            'Weight',
            'Father\'s Name',
            'Mother\'s Name',
            'Name of Spouse',
            'Spouse\'s Date of Birth',
            'Spouse\'s Occupation',
            'Date of Marriage',
            'Children (Name and Birthday)',
            'Person to Contact Name',
            'Person to Contact Address',
            'Person to Contact Number',
            'Person to Contact Relationship',
            'Primary Education',
            'Secondary Education',
            'Tertiary Education',
            'SSS #',
            'Philhealth #',
            'Pag-ibig #',
            'TIN',
            'Current Work Location (Department name/ Project Code)',
            'Current Position', 'Salary Grade'
        ];
        if ($validated) {
            switch ($validated["report_type"]) {
                case AdministrativeReport::EMPLOYEE_MASTERLIST->value:
                    $reportData = ReportService::employeeMasterListExport($validated);
                    $excel = SimpleExcelWriter::create("master_list.xlsx");
                    $excel->addHeader($masterListHeaders);
                    foreach ($reportData as $row) {
                        $excel->addRow($row);
                    }
                    $excel->close();
                    Storage::disk('public')->delete('master_list.xlsx', now()->addMinutes(5));
                    return response()->json(
                        [
                            "success" => true,
                            'url' => "/master_list.xlsx",
                            'message' => "Successfully Download."
                        ]);
                    break;
                default:
                    return new JsonResponse([
                        "success" => false,
                        'message' => "File not found."], 400
                    );
            }
        }
    }
}

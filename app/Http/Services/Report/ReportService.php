<?php

namespace App\Http\Services\Report;

use App\Enums\Reports\LoanReports;
use App\Enums\GroupType;
use App\Enums\PanRequestType;
use App\Http\Resources\DefaultReportPaymentResource;
use App\Http\Resources\HdmfEmployeeLoansResource;
use App\Http\Resources\HdmfGroupSummaryLoansResource;
use App\Http\Resources\PagibigEmployeeRemittanceResource;
use App\Http\Resources\PagibigGroupRemittanceResource;
use App\Http\Resources\PagibigRemittanceSummaryResource;
use App\Http\Resources\PhilhealthEmployeeRemittanceResource;
use App\Http\Resources\PhilhealthGroupRemittanceResource;
use App\Http\Resources\philhealthRemittanceSummaryResource;
use App\Http\Resources\SssEmployeeLoanResource;
use App\Http\Resources\SSSEmployeeRemittanceResource;
use App\Http\Resources\SssGroupRemittanceResource;
use App\Http\Resources\SssGroupSummaryLoansResource;
use App\Http\Resources\SssRemittanceSummaryResource;
use App\Http\Resources\Reports\AdministrativeEmployeeTenureship;
use App\Http\Resources\Reports\AdministrativeEmployeeMasterList;
use App\Http\Resources\Reports\AdministrativeEmployeeNewList;
use App\Http\Resources\Reports\AdministrativeEmployeeLeaves;
use App\Http\Resources\Reports\PortalMonitoringOvertime;
use App\Http\Resources\Reports\PortalMonitoringOvertimeSummary;
use App\Http\Resources\Reports\PortalMonitoringSalary;
use App\Http\Resources\Reports\PortalMonitoringFailureToLog;
use App\Http\Resources\Reports\PortalMonitoringFailureToLogSummary;
use App\Http\Resources\Reports\PortalMonitoringLeave;
use App\Http\Resources\Reports\PortalMonitoringLeaveSummary;
use App\Http\Resources\Reports\PortalMonitoringTravelOrder;
use App\Http\Resources\Reports\PortalMonitoringTravelOrderSummary;
use App\Http\Resources\Reports\PortalMonitoringManpowerRequest;
use App\Http\Resources\Reports\PortalMonitoringManpowerRequestSummary;
use App\Http\Resources\Reports\PortalMonitoringPanTermination;
use App\Http\Resources\Reports\PortalMonitoringPanTransfer;
use App\Http\Resources\Reports\PortalMonitoringPanPromotion;
use App\Models\TravelOrder;
use App\Models\Loans;
use App\Models\OtherDeduction;
use App\Models\PayrollDetail;
use App\Models\Employee;
use App\Models\Overtime;
use App\Models\PayrollRecord;
use App\Models\AllowanceRequest;
use App\Models\FailureToLog;
use App\Models\ManpowerRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Http\Resources\CompressedImageResource;
use App\Http\Services\Payroll\SalaryMonitoringReportService;
use App\Models\EmployeeLeaves;
use App\Models\EmployeePanRequest;

class ReportService
{
    /*
    * REMITTANCE REPORTS
    */
    public static function sssEmployeeRemittance($validatedData = [])
    {
        $data = PayrollDetail::with(["employee.company_employments"])
            ->whereHas('payroll_record', function ($query) use ($validatedData) {
                return $query->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                    ->isApproved();
            })
            ->hasSssContributions()
            ->orderBy("created_at", "DESC")
            ->get()
            ->append([
                "total_sss_contribution",
                "total_sss_compensation",
                "total_sss_wisp",
                "total_sss",
            ])
            ->sortBy('employee.fullname_last', SORT_NATURAL)
            ->groupBy("employee_id")
            ->map(function ($employeeData) {
                return [
                    ...$employeeData->first()->toArray(),
                    'sss_employer_contribution' => $employeeData->sum("sss_employer_contribution"),
                    'sss_employee_contribution' => $employeeData->sum("sss_employee_contribution"),
                    'sss_employer_compensation' => $employeeData->sum("sss_employer_compensation"),
                    'sss_employee_compensation' => $employeeData->sum("sss_employee_compensation"),
                    'sss_employer_wisp' => $employeeData->sum("sss_employer_wisp"),
                    'sss_employee_wisp' => $employeeData->sum("sss_employee_wisp"),
                    'total_sss_contribution' => $employeeData->sum("total_sss_contribution"),
                    'total_sss_compensation' => $employeeData->sum("total_sss_compensation"),
                    'total_sss_wisp' => $employeeData->sum("total_sss_wisp"),
                    'total_sss' => $employeeData->sum("total_sss"),
                ];
            })
            ->values()
            ->all();
        return [
            'success' => true,
            'message' => 'SSS Employee Remittance fetched successfully.',
            'data' => SSSEmployeeRemittanceResource::collection($data),
        ];
    }
    public static function sssGroupRemittance($validatedData = [])
    {
        $data = PayrollDetail::with(['payroll_record', "employee.company_employments"])
            ->whereHas('payroll_record', function ($query) use ($validatedData) {
                return $query->when(!empty($validatedData['project_id']), function ($query2) use ($validatedData) {
                    return $query2->where('project_id', $validatedData["project_id"]);
                })
                    ->when(!empty($validatedData['department_id']), function ($query2) use ($validatedData) {
                        return $query2->where('department_id', $validatedData["department_id"]);
                    })
                    ->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                    ->isApproved();
            })
            ->hasSssContributions()
            ->orderBy("created_at", "DESC")
            ->get()
            ->append([
                "total_sss_contribution",
                "total_sss_compensation",
                "total_sss_wisp",
                "total_sss",
            ])
            ->sortBy('employee.fullname_last', SORT_NATURAL)
            ->groupBy("employee_id")
            ->map(function ($employeeData) {
                return [
                    ...$employeeData->first()->toArray(),
                    'sss_employer_contribution' => $employeeData->sum("sss_employer_contribution"),
                    'sss_employee_contribution' => $employeeData->sum("sss_employee_contribution"),
                    'sss_employer_compensation' => $employeeData->sum("sss_employer_compensation"),
                    'sss_employee_compensation' => $employeeData->sum("sss_employee_compensation"),
                    'sss_employer_wisp' => $employeeData->sum("sss_employer_wisp"),
                    'sss_employee_wisp' => $employeeData->sum("sss_employee_wisp"),
                    'total_sss_contribution' => $employeeData->sum("total_sss_contribution"),
                    'total_sss_compensation' => $employeeData->sum("total_sss_compensation"),
                    'total_sss_wisp' => $employeeData->sum("total_sss_wisp"),
                    'total_sss' => $employeeData->sum("total_sss"),
                    "payroll_record" => [
                        ...$employeeData->first()->payroll_record->toArray(),
                        "charging_name" => $employeeData->first()->payroll_record->charging_name,
                    ],
                ];
            })
            ->values()
            ->all();
        $data = collect($data);
        $firstRecord = $data->first();
        $dataArray = $data->all();
        return [
            'success' => true,
            'message' => 'SSS Group Remittance Request fetched.',
            'data' => [
                'charging' => $firstRecord['payroll_record']['charging_name'] ?? "",
                'remittances' => SssGroupRemittanceResource::collection($dataArray)
            ],
        ];
    }
    public static function sssRemittanceSummary($validatedData = [])
    {
        $data = PayrollDetail::with(['payroll_record', "employee.company_employments"])
            ->whereHas('payroll_record', function ($query) use ($validatedData) {
                return $query
                    ->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                    ->isApproved();
            })
            ->hasSssContributions()
            ->orderBy("created_at", "DESC")
            ->get()
            ->append([
                "total_sss_contribution",
                "total_sss_compensation",
                "total_sss_wisp",
                "total_sss",
            ])
            ->sortBy('payroll_record.charging_name', SORT_NATURAL)
            ->values()
            ->all();
        $uniqueGroup =  collect($data)->groupBy('payroll_record.charging_name');
        return [
            'success' => true,
            'message' => 'SSS Group Remittance Request fetched.',
            'data' => SssRemittanceSummaryResource::collection($uniqueGroup),
        ];
    }
    public static function pagibigEmployeeRemittance($validatedData = [])
    {
        $data = PayrollDetail::with(["employee.company_employments"])
            ->whereHas('payroll_record', function ($query) use ($validatedData) {
                return $query->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                    ->isApproved();
            })
            ->hasPagibigContributions()
            ->orderBy("created_at", "DESC")
            ->get()
            ->append([
                "total_pagibig_contribution",
            ])
            ->sortBy('employee.fullname_last', SORT_NATURAL)
            ->groupBy("employee_id")
            ->map(function ($employeeData) {
                return [
                    ...$employeeData->first()->toArray(),
                    'pagibig_employee_contribution' => $employeeData->sum("pagibig_employee_contribution"),
                    'pagibig_employer_contribution' => $employeeData->sum("pagibig_employer_contribution"),
                    'total_pagibig_contribution' => $employeeData->sum("total_pagibig_contribution"),
                ];
            })
            ->values()
            ->all();
        return [
            'success' => true,
            'message' => 'Employee Remittance Request fetched.',
            'data' => PagibigEmployeeRemittanceResource::collection($data),
        ];
    }
    public static function pagibigGroupRemiitance($validatedData = [])
    {
        $data = PayrollDetail::with(['payroll_record', "employee.company_employments"])
            ->whereHas('payroll_record', function ($query) use ($validatedData) {
                return $query->when(!empty($validatedData['project_id']), function ($query2) use ($validatedData) {
                    return $query2->where('project_id', $validatedData["project_id"]);
                })
                    ->when(!empty($validatedData['department_id']), function ($query2) use ($validatedData) {
                        return $query2->where('department_id', $validatedData["department_id"]);
                    })
                    ->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                    ->isApproved();
            })
            ->hasPagibigContributions()
            ->orderBy("created_at", "DESC")
            ->get()
            ->append([
                "total_pagibig_contribution",
            ])
            ->sortBy('employee.fullname_last', SORT_NATURAL)
            ->groupBy("employee_id")
            ->map(function ($employeeData) {
                return [
                    ...$employeeData->first()->toArray(),
                    'pagibig_employee_contribution' => $employeeData->sum("pagibig_employee_contribution"),
                    'pagibig_employer_contribution' => $employeeData->sum("pagibig_employer_contribution"),
                    'total_pagibig_contribution' => $employeeData->sum("total_pagibig_contribution"),
                    "payroll_record" => [
                        ...$employeeData->first()->payroll_record->toArray(),
                        "charging_name" => $employeeData->first()->payroll_record->charging_name,
                    ],
                ];
            })
            ->values()
            ->all();
        $data = collect($data);
        $firstRecord = $data->first();
        $dataArray = $data->all();
        return [
            'success' => true,
            'message' => 'Pagibig Group Remittance Request fetched.',
            'data' => [
                'charging' => $firstRecord['payroll_record']['charging_name'] ?? "",
                'remittances' => PagibigGroupRemittanceResource::collection($dataArray)
            ],
        ];
    }
    public static function pagibigRemittanceSummary($validatedData = [])
    {
        $data = PayrollDetail::with(['payroll_record', "employee.company_employments"])
            ->whereHas('payroll_record', function ($query) use ($validatedData) {
                return $query
                    ->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                    ->isApproved();
            })
            ->hasPagibigContributions()
            ->orderBy("created_at", "DESC")
            ->get()
            ->append([
                "total_pagibig_contribution",
            ])
            ->sortBy('payroll_record.charging_name', SORT_NATURAL)
            ->values()
            ->all();
        $uniqueGroup =  collect($data)->groupBy('payroll_record.charging_name');
        return [
            'success' => true,
            'message' => 'SSS Group Remittance Request fetched.',
            'data' => PagibigRemittanceSummaryResource::collection($uniqueGroup),
        ];
    }
    public static function philhealthEmployeeRemittance($validatedData = [])
    {
        $data = PayrollDetail::with(["employee.company_employments"])
            ->whereHas('payroll_record', function ($query) use ($validatedData) {
                return $query->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                    ->isApproved();
            })
            ->hasPhilhealthContributions()
            ->get()
            ->append([
                "total_philhealth_contribution"
            ])
            ->sortBy('employee.fullname_last', SORT_NATURAL)
            ->groupBy("employee_id")
            ->map(function ($employeeData) {
                return [
                    ...$employeeData->first()->toArray(),
                    'philhealth_employee_contribution' => $employeeData->sum("philhealth_employee_contribution"),
                    'philhealth_employer_contribution' => $employeeData->sum("philhealth_employer_contribution"),
                    'total_philhealth_contribution' => $employeeData->sum("total_philhealth_contribution"),
                ];
            })
            ->values()
            ->all();
        return [
            'success' => true,
            'message' => 'PhilHealth Employee Remittance fetched successfully.',
            'data' => PhilhealthEmployeeRemittanceResource::collection($data),
        ];
    }
    public static function philhealthGroupRemittance($validatedData = [])
    {
        $data = PayrollDetail::with(['payroll_record', "employee.company_employments"])
            ->whereHas('payroll_record', function ($query) use ($validatedData) {
                return $query->when(!empty($validatedData['project_id']), function ($query2) use ($validatedData) {
                    return $query2->where('project_id', $validatedData["project_id"]);
                })
                    ->when(!empty($validatedData['department_id']), function ($query2) use ($validatedData) {
                        return $query2->where('department_id', $validatedData["department_id"]);
                    })
                    ->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                    ->isApproved();
            })
            ->hasPhilhealthContributions()
            ->orderBy("created_at", "DESC")
            ->get()
            ->append([
                "total_philhealth_contribution"
            ])
            ->sortBy('employee.fullname_last', SORT_NATURAL)
            ->groupBy("employee_id")
            ->map(function ($employeeData) {
                return [
                    ...$employeeData->first()->toArray(),
                    'philhealth_employee_contribution' => $employeeData->sum("philhealth_employee_contribution"),
                    'philhealth_employer_contribution' => $employeeData->sum("philhealth_employer_contribution"),
                    'total_philhealth_contribution' => $employeeData->sum("total_philhealth_contribution"),
                    "payroll_record" => [
                        ...$employeeData->first()->payroll_record->toArray(),
                        "charging_name" => $employeeData->first()->payroll_record->charging_name,
                    ],
                ];
            })
            ->values()
            ->all();
        $data = collect($data);
        $firstRecord = $data->first();
        $dataArray = $data->all();
        return [
            'success' => true,
            'message' => 'Philhealth Group Remittance Request fetched.',
            'data' => [
                'charging' => $firstRecord['payroll_record']['charging_name'] ?? "",
                'remittances' => PhilhealthGroupRemittanceResource::collection($dataArray)
            ],
        ];
    }
    public static function philhealthRemittanceSummary($validatedData = [])
    {
        $data = PayrollDetail::with(['payroll_record', "employee.company_employments"])
            ->whereHas('payroll_record', function ($query) use ($validatedData) {
                return $query
                    ->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                    ->isApproved();
            })
            ->hasPhilhealthContributions()
            ->orderBy("created_at", "DESC")
            ->get()
            ->append([
                "total_philhealth_contribution"
            ])
            ->sortBy('payroll_record.charging_name', SORT_NATURAL)
            ->values()
            ->all();
        $uniqueGroup =  collect($data)->groupBy('payroll_record.charging_name');
        return [
            'success' => true,
            'message' => 'Philhealth Remittance Request fetched.',
            'data' => philhealthRemittanceSummaryResource::collection($uniqueGroup),
        ];
    }
    /*
    * LOAN REPORTS
    */
    public static function sssEmployeeLoans($validatedData = [])
    {
        $data = PayrollDetail::with(["employee.company_employments"])
            ->whereHas('payroll_record', function ($query) use ($validatedData) {
                return $query->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                    ->isApproved();
            })
            ->whereHas('loanPayments', function ($query) {
                return $query->where('name', LoanReports::SSS->value);
            })
            ->orderBy("created_at", "DESC")
            ->get()
            ->sortBy('employee.fullname_last', SORT_NATURAL)
            ->groupBy("employee_id")
            ->map(function ($employeeData) use ($validatedData) {
                return [
                    ...$employeeData->first()->toArray(),
                    "employee_name" => $employeeData->first()->employee->fullname_last,
                    "employee_sss_id" => $employeeData->first()->employee->company_employments->sss_number,
                    "total_payments" => $employeeData->first()->loanPayments()->where('name', $validatedData['loan_type'])->sum("amount"),
                ];
            })
            ->values()
            ->all();
        return [
            'success' => true,
            'message' => 'SSS Employee Loan Request fetched.',
            'data' => SssEmployeeLoanResource::collection($data),
        ];
    }
    public static function sssGroupSummaryLoans($validatedData = [])
    {
        $data = PayrollDetail::with(["employee.company_employments", "payroll_record"])
            ->whereHas('payroll_record', function ($query) use ($validatedData) {
                return $query
                    ->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                    ->isApproved();
            })
            ->whereHas('loanPayments', function ($query) {
                return $query->where('name', LoanReports::SSS->value);
            })
            ->hasSssContributions()
            ->get()
            ->append([
                "total_sss_contribution",
                "total_sss_compensation",
                "total_sss_wisp",
                "total_sss",
            ])
            ->groupBy("employee_id")
            ->map(function ($employeeData) use ($validatedData) {
                return [
                    ...$employeeData->first()->toArray(),
                    "employee_name" => $employeeData->first()->employee->fullname_last,
                    "employee_sss_id" => $employeeData->first()->employee->company_employments->sss_number,
                    "total_group_amount" => $employeeData->sum(function ($detail) use ($validatedData) {
                        return $detail->loanPayments()->where('name', $validatedData['loan_type'])->sum('amount');
                    }),
                    "total_amount" => $employeeData->first()->loanPayments()->where('name', $validatedData['loan_type'])->sum("amount"),
                    "loan_type" => $employeeData->first()->loanPayments?->first()?->name,
                    "payroll_record" => [
                        ...$employeeData->first()->payroll_record->toArray(),
                        "charging_name" => $employeeData->first()->payroll_record->charging_name,
                    ],
                ];
            })
            ->sortBy("payroll_record.charging_name", SORT_NATURAL)
            ->sortBy("employee_name", SORT_NATURAL)
            ->values()
            ->all();
        return [
            'success' => true,
            'message' => 'Employee Remittance Request fetched.',
            'data' => SssGroupSummaryLoansResource::collection($data),
        ];
    }
    public static function hdmfEmployeeLoans($validatedData = [])
    {
        $data = PayrollDetail::with(["employee.company_employments"])
            ->whereHas('payroll_record', function ($query) use ($validatedData) {
                return $query->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                    ->isApproved();
            })
            ->whereHas('loanPayments', function ($query) {
                return $query->where('name', LoanReports::HDMF_MPL->value);
            })
            ->orderBy("created_at", "DESC")
            ->get()
            ->sortBy('employee.fullname_last', SORT_NATURAL)
            ->groupBy("employee_id")
            ->map(function ($employeeData) use ($validatedData) {
                return [
                    ...$employeeData->first()->toArray(),
                    "employee_pagibig_no" => $employeeData->first()->employee->company_employments->pagibig_number,
                    "first_name" => $employeeData->first()->employee->first_name,
                    "middle_name" => $employeeData->first()->employee->middle_name,
                    "last_name" => $employeeData->first()->employee->family_name,
                    "suffix_name" => $employeeData->first()->employee->suffix_name,
                    "loan_type" => $employeeData->first()->loanPayments?->first()?->name,
                    "percov" => $validatedData['filter_month'] . $validatedData['filter_year'],
                    "total_payments" => $employeeData->sum(function ($detail) use ($validatedData) {
                        return $detail->loanPayments()->where('name', $validatedData['loan_type'])->sum('amount');
                    }),
                ];
            })
            ->values()
            ->all();
        return [
            'success' => true,
            'message' => 'Pagibig Employee Remittance Request fetched.',
            'data' => HdmfEmployeeLoansResource::collection($data),
        ];
    }
    public static function hdmfGroupSummaryLoans($validatedData = [])
    {
        $data = PayrollDetail::with(["employee.company_employments", "payroll_record"])
            ->whereHas('payroll_record', function ($query) use ($validatedData) {
                return $query->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                    ->isApproved();
            })
            ->whereHas('loanPayments', function ($query) {
                return $query->where('name', LoanReports::HDMF_MPL->value);
            })
            ->get()
            ->groupBy("employee_id")
            ->map(function ($employeeData) use ($validatedData) {
                return [
                    ...$employeeData->first()->toArray(),
                    "employee_name" => $employeeData->first()->employee->fullname_last,
                    "employee_sss_id" => $employeeData->first()->employee->company_employments->sss_number,
                    "first_name" => $employeeData->first()->employee->first_name,
                    "middle_name" => $employeeData->first()->employee->middle_name,
                    "last_name" => $employeeData->first()->employee->family_name,
                    "suffix_name" => $employeeData->first()->employee->suffix_name,
                    "loan_type" => $employeeData->first()->loanPayments?->first()?->name,
                    "total_payments" => $employeeData->sum(function ($detail) use ($validatedData) {
                        return $detail->loanPayments()->where('name', $validatedData['loan_type'])->sum('amount');
                    }),
                    "payroll_record" => [
                        ...$employeeData->first()->payroll_record->toArray(),
                        "charging_name" => $employeeData->first()->payroll_record->charging_name,
                    ],
                ];
            })
            ->sortBy("payroll_record.charging_name", SORT_NATURAL)
            ->values()
            ->all();
        return [
            'success' => true,
            'message' => 'Employee Remittance Request fetched.',
            'data' => HdmfGroupSummaryLoansResource::collection($data),
        ];
    }
    public static function coopEmployeeLoans($validatedData = [])
    {
        $data = PayrollDetail::with(["employee.company_employments"])
            ->whereHas('payroll_record', function ($query) use ($validatedData) {
                return $query->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                    ->isApproved();
            })
            ->whereHas('loanPayments', function ($query) {
                return $query->where('name', LoanReports::COOP->value);
            })
            ->orderBy("created_at", "DESC")
            ->get()
            ->sortBy('employee.fullname_last', SORT_NATURAL)
            ->groupBy("employee_id")
            ->map(function ($employeeData) use ($validatedData) {
                return [
                    ...$employeeData->first()->toArray(),
                    "employee_pagibig_no" => $employeeData->first()->employee->company_employments->pagibig_number,
                    "first_name" => $employeeData->first()->employee->first_name,
                    "middle_name" => $employeeData->first()->employee->middle_name,
                    "last_name" => $employeeData->first()->employee->family_name,
                    "suffix_name" => $employeeData->first()->employee->suffix_name,
                    "loan_type" => $employeeData->first()->loanPayments?->first()?->name,
                    "percov" => $validatedData['filter_month'] . $validatedData['filter_year'],
                    "total_payments" => $employeeData->first()->loanPayments()->where('name', $validatedData['loan_type'])->sum("amount"),
                ];
            })
            ->values()
            ->all();
        return [
            'success' => true,
            'message' => 'Employee Remittance Request fetched.',
            'data' => HdmfEmployeeLoansResource::collection($data),
        ];
    }
    public static function coopGroupSummaryLoans($validatedData = [])
    {
        $data = PayrollDetail::with(["employee.company_employments"])
            ->whereHas('payroll_record', function ($query) use ($validatedData) {
                return $query->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                    ->isApproved();
            })
            ->whereHas('loanPayments', function ($query) {
                return $query->where('name', LoanReports::COOP->value);
            })
            ->orderBy("created_at", "DESC")
            ->get()
            ->sortBy('employee.fullname_last', SORT_NATURAL)
            ->groupBy("employee_id")
            ->map(function ($employeeData) use ($validatedData) {
                return [
                    ...$employeeData->first()->toArray(),
                    "employee_pagibig_no" => $employeeData->first()->employee->company_employments->pagibig_number,
                    "first_name" => $employeeData->first()->employee->first_name,
                    "middle_name" => $employeeData->first()->employee->middle_name,
                    "last_name" => $employeeData->first()->employee->family_name,
                    "suffix_name" => $employeeData->first()->employee->suffix_name,
                    "loan_type" => $employeeData->first()->loanPayments?->first()?->name,
                    "percov" => $validatedData['filter_month'] . $validatedData['filter_year'],
                    "total_payments" => $employeeData->first()->loanPayments()->sum("amount"),
                ];
            })
            ->values()
            ->all();
        return [
            'success' => true,
            'message' => 'Employee Remittance Request fetched.',
            'data' => HdmfEmployeeLoansResource::collection($data),
        ];
    }
    public static function getDefaultLoanPayments($validatedData = [])
    {
        $data = PayrollDetail::with(["employee.company_employments"])
            ->whereHas('payroll_record', function ($query) use ($validatedData) {
                return $query->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                    ->isApproved();
            })
            ->whereHas('loanPayments', function ($query) use ($validatedData) {
                return $query->where('name', $validatedData['loan_type']);
            })
            ->orderBy("created_at", "DESC")
            ->get()
            ->sortBy('employee.fullname_last', SORT_NATURAL)
            ->groupBy("employee_id")
            ->map(function ($employeeData) use ($validatedData) {
                return [
                    ...$employeeData->first()->toArray(),
                    "employee_fullname" => $employeeData->first()->employee->fullname_first,
                    "total_payments" => $employeeData->first()->loanPayments()->where('name', $validatedData['loan_type'])->sum("amount"),
                ];
            })
            ->values()
            ->all();
        return [
            'success' => true,
            'message' => 'Employee Remittance Request fetched.',
            'data' => DefaultReportPaymentResource::collection($data),
        ];
    }
    public static function getDefaultLoanPaymentsGroup($validatedData = [])
    {
        $data = PayrollDetail::with(["employee.company_employments"])
            ->whereHas('payroll_record', function ($query) use ($validatedData) {
                return $query->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                    ->isApproved();
            })
            ->whereHas('loanPayments', function ($query) use ($validatedData) {
                return $query->where('name', $validatedData['loan_type']);
            })
            ->orderBy("created_at", "DESC")
            ->get()
            ->sortBy('employee.fullname_last', SORT_NATURAL)
            ->groupBy("employee_id")
            ->map(function ($employeeData) use ($validatedData) {
                return [
                    ...$employeeData->first()->toArray(),
                    "employee_fullname" => $employeeData->first()->employee->fullname_first,
                    "total_group_amount" => $employeeData->sum(function ($detail) use ($validatedData) {
                        return $detail->loanPayments()->where('name', $validatedData['loan_type'])->sum('amount');
                    }),
                    "total_amount" => $employeeData->first()->loanPayments()->where('name', $validatedData['loan_type'])->sum("amount"),
                    "payroll_record" => [
                        ...$employeeData->first()->payroll_record->toArray(),
                        "charging_name" => $employeeData->first()->payroll_record->charging_name,
                    ],
                ];
            })
            ->values()
            ->all();
        return [
            'success' => true,
            'message' => 'Employee Remittance Request fetched.',
            'data' => DefaultReportPaymentResource::collection($data),
        ];
    }
    public static function hdmfCalamityEmployeeLoans($validatedData = [])
    {
        $data = PayrollDetail::with(["employee.company_employments"])
            ->whereHas('payroll_record', function ($query) use ($validatedData) {
                return $query->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                    ->isApproved();
            })
            ->whereHas('loanPayments', function ($query) {
                return $query->where('name', LoanReports::CALAMITY_LOAN->value);
            })
            ->orderBy("created_at", "DESC")
            ->get()
            ->sortBy('employee.fullname_last', SORT_NATURAL)
            ->groupBy("employee_id")
            ->map(function ($employeeData) use ($validatedData) {
                return [
                    ...$employeeData->first()->toArray(),
                    "employee_pagibig_no" => $employeeData->first()->employee->company_employments->pagibig_number,
                    "first_name" => $employeeData->first()->employee->first_name,
                    "middle_name" => $employeeData->first()->employee->middle_name,
                    "last_name" => $employeeData->first()->employee->family_name,
                    "suffix_name" => $employeeData->first()->employee->suffix_name,
                    "loan_type" => $employeeData->first()->loanPayments?->first()?->name,
                    "percov" => $validatedData['filter_month'] . $validatedData['filter_year'],
                    "total_payments" => $employeeData->first()->loanPayments()->where('name', $validatedData['loan_type'])->sum("amount"),
                ];
            })
            ->values()
            ->all();
        return [
            'success' => true,
            'message' => 'Employee Remittance Request fetched.',
            'data' => HdmfEmployeeLoansResource::collection($data),
        ];
    }
    public static function hdmfCalamityGroupSummaryLoans($validatedData = [])
    {
        $data = PayrollDetail::with(["employee.company_employments"])
            ->whereHas('payroll_record', function ($query) use ($validatedData) {
                return $query->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                    ->isApproved();
            })
            ->whereHas('loanPayments', function ($query) {
                return $query->where('name', LoanReports::CALAMITY_LOAN->value);
            })
            ->orderBy("created_at", "DESC")
            ->get()
            ->sortBy('employee.fullname_last', SORT_NATURAL)
            ->groupBy("employee_id")
            ->map(function ($employeeData) use ($validatedData) {
                return [
                    ...$employeeData->first()->toArray(),
                    "employee_pagibig_no" => $employeeData->first()->employee->company_employments->pagibig_number,
                    "first_name" => $employeeData->first()->employee->first_name,
                    "middle_name" => $employeeData->first()->employee->middle_name,
                    "last_name" => $employeeData->first()->employee->family_name,
                    "suffix_name" => $employeeData->first()->employee->suffix_name,
                    "loan_type" => $employeeData->first()->loanPayments?->first()?->name,
                    "percov" => $validatedData['filter_month'] . $validatedData['filter_year'],
                    "total_payments" => $employeeData->first()->loanPayments()->sum("amount"),
                ];
            })
            ->values()
            ->all();
        return [
            'success' => true,
            'message' => 'Employee Remittance Request fetched.',
            'data' => HdmfEmployeeLoansResource::collection($data),
        ];
    }
    /*
    * LOAN REPORTS
    */
    public static function getLoanCategoryList()
    {
        return [
            'success' => true,
            'message' => 'Loan Category List fetched successfully.',
            'data' => Loans::select('name')->distinct()->orderBy('name', 'ASC')->get(),
        ];
    }
    public static function getLoanEmployeeReport($validatedData)
    {
        return PayrollDetail::with(["employee.company_employments"])
            ->with([
                'loanPayments' => function ($query) use ($validatedData) {
                    return $query->where("name", $validatedData["loan_type"]);
                }
            ])
            ->whereHas('payroll_record', function ($query) use ($validatedData) {
                return $query->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                    ->isApproved();
            })
            ->whereHas('loanPayments', function ($query) use ($validatedData) {
                return $query->where('name', $validatedData['loan_type']);
            })
            ->orderBy("created_at", "DESC")
            ->get()
            ->sortBy('employee.fullname_last', SORT_NATURAL)
            ->groupBy("employee_id")
            ->map(function ($employeeData) use ($validatedData) {
                $totalLoanPayments = $employeeData->sum(function ($detail) use ($validatedData) {
                    return $detail->loanPayments()->where('name', $validatedData['loan_type'])->sum('amount');
                });
                return [
                    ...$employeeData->first()->toArray(),
                    "first_name" => $employeeData->first()->employee->first_name,
                    "middle_name" => $employeeData->first()->employee->middle_name,
                    "last_name" => $employeeData->first()->employee->family_name,
                    "suffix" => $employeeData->first()->employee->suffix_name,
                    "fullname" => $employeeData->first()->employee->fullname_last,
                    "loan_type" => $employeeData->first()->loanPayments()->where('name', $validatedData['loan_type'])->first()?->name,
                    "total_payments" => $totalLoanPayments,
                    "sss_no" => $employeeData->first()->employee->company_employments->sss_number,
                    "loan_account_no" => "",
                    "pagibig_no" => $employeeData->first()->employee->company_employments->pagibig_number,
                    "application_no" => "",
                    "percov" => $validatedData['filter_month'] . $validatedData['filter_year'],
                ];
            })
            ->values()
            ->all();
    }
    public static function getLoanGroupReport($validatedData)
    {
        $data = PayrollDetail::with(["payroll_record", "employee.company_employments"])
            ->with([
                'loanPayments' => function ($query) use ($validatedData) {
                    return $query->where("name", $validatedData["loan_type"]);
                }
            ])
            ->whereHas('payroll_record', function ($query) use ($validatedData) {
                return $query->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                    ->isApproved();
            })
            ->whereHas('loanPayments', function ($query) use ($validatedData) {
                return $query->where('name', $validatedData['loan_type']);
            })
            ->orderBy("created_at", "DESC")
            ->get()
            ->sortBy('payroll_record.charging_name', SORT_NATURAL)
            ->map(function ($employeeData) use ($validatedData) {
                return [
                    ...$employeeData->toArray(),
                    "loan_type" => $employeeData->loanPayments()->where('name', $validatedData['loan_type'])->first()?->name,
                    "total_payments" => $employeeData->loanPayments()->where('name', $validatedData['loan_type'])->sum("amount"),
                    "percov" => $validatedData['filter_month'] . $validatedData['filter_year'],
                    "sss_no" => $employeeData->employee->company_employments->sss_number,
                    "payroll_record" => [
                        ...$employeeData->payroll_record->toArray(),
                        "charging_name" => $employeeData->payroll_record->charging_name,
                    ],
                ];
            })
            ->values()
            ->all();
        $uniqueGroup =  collect($data)->groupBy('payroll_record.charging_name');
        return $uniqueGroup;
    }
    /*
    * OTHER DEDUCTION REPORTS
    */
    public static function otherDeductionsCategoryList()
    {
        return [
            'success' => true,
            'message' => 'Other Deduction Category List fetched successfully.',
            'data' => OtherDeduction::select('otherdeduction_name')->distinct()->orderBy('otherdeduction_name', 'ASC')->get(),
        ];
    }
    public static function getOtherDeductionEmployeeReport($validatedData)
    {
        return PayrollDetail::with(["employee.company_employments"])
            ->with([
                'otherDeductionPayments' => function ($query) use ($validatedData) {
                    return $query->where("name", $validatedData["loan_type"]);
                }
            ])
            ->whereHas('payroll_record', function ($query) use ($validatedData) {
                return $query->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                    ->isApproved();
            })
            ->whereHas('otherDeductionPayments', function ($query) use ($validatedData) {
                return $query->where('name', $validatedData['loan_type']);
            })
            ->orderBy("created_at", "DESC")
            ->get()
            ->sortBy('employee.fullname_last', SORT_NATURAL)
            ->groupBy("employee_id")
            ->map(function ($employeeData) use ($validatedData) {
                $totalLoanPayments = $employeeData->sum(function ($detail) use ($validatedData) {
                    return $detail->otherDeductionPayments()->where('name', $validatedData['loan_type'])->sum('amount');
                });
                return [
                    ...$employeeData->first()->toArray(),
                    "employee_pagibig_no" => $employeeData->first()->employee->company_employments->pagibig_number,
                    "first_name" => $employeeData->first()->employee->first_name,
                    "middle_name" => $employeeData->first()->employee->middle_name,
                    "last_name" => $employeeData->first()->employee->family_name,
                    "suffix" => $employeeData->first()->employee->name_suffix,
                    "fullname" => $employeeData->first()->employee->fullname_last,
                    "loan_type" => $employeeData->first()->otherDeductionPayments()->where('name', $validatedData['loan_type'])->first()?->name,
                    "total_payments" => $totalLoanPayments,
                    "percov" => $validatedData['filter_month'] . $validatedData['filter_year'],
                ];
            })
            ->values()
            ->all();
    }
    public static function getOtherDeductionGroupReport($validatedData)
    {
        $data = PayrollDetail::with(["payroll_record", "employee.company_employments"])
            ->with([
                'otherDeductionPayments' => function ($query) use ($validatedData) {
                    return $query->where("name", $validatedData["loan_type"]);
                }
            ])
            ->whereHas('payroll_record', function ($query) use ($validatedData) {
                return $query->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                    ->isApproved();
            })
            ->whereHas('otherDeductionPayments', function ($query) use ($validatedData) {
                return $query->where('name', $validatedData['loan_type']);
            })
            ->orderBy("created_at", "DESC")
            ->get()
            ->sortBy('payroll_record.charging_name', SORT_NATURAL)
            ->map(function ($employeeData) use ($validatedData) {
                return [
                    ...$employeeData->toArray(),
                    "loan_type" => $employeeData->otherDeductionPayments()->where('name', $validatedData['loan_type'])->first()?->name,
                    "total_payments" => $employeeData->otherDeductionPayments()->where('name', $validatedData['loan_type'])->sum("amount"),
                    "percov" => $validatedData['filter_month'] . $validatedData['filter_year'],
                    "payroll_record" => [
                        ...$employeeData->payroll_record->toArray(),
                        "charging_name" => $employeeData->payroll_record->charging_name,
                    ],
                ];
            })
            ->values()
            ->all();
        $uniqueGroup =  collect($data)->groupBy('payroll_record.charging_name');
        return $uniqueGroup;
    }
    public static function employeeTenureshipList($validate)
    {
        $data = Employee::isActive()->with("current_employment")->get();
        if ($validate["group_type"] !== GroupType::ALL->value) {
            $workLocation = ($validate["group_type"] === 'Department') ? "Office" : "Project Code";
            $type = ($validate["group_type"] === 'Department') ? "department" : "projects";
            $givenId = ($validate["group_type"] === 'Department') ? $validate["department_id"] : $validate["project_id"];
            $data = Employee::isActive()->with("current_employment")->whereHas(
                "current_employment",
                function ($query) use ($workLocation, $type, $givenId) {
                    $query->where('work_location', $workLocation)->whereHas(
                        $type,
                        function ($query) use ($type, $givenId) {
                            if ($givenId) {
                                if ($type === "department") {
                                    $query->where("departments.id", $givenId);
                                }
                                if ($type === "projects") {
                                    $query->where("projects.id", $givenId);
                                }
                            }
                        }
                    );
                }
            )
                ->get();
        }
        return AdministrativeEmployeeTenureship::collection($data);
    }

    public static function employeeMasterList($validate)
    {
        $data = Employee::isActive()->with(
            "current_employment",
            "present_address",
            "permanent_address",
            "father",
            "mother",
            "spouse",
            "child",
            "contact_person",
            "employee_education_elementary",
            "employee_education_secondary",
            "employee_education_college",
            "company_employments",
        )->get();
        if ($validate["group_type"] !== GroupType::ALL->value) {
            $workLocation = ($validate["group_type"] === 'Department') ? "Office" : "Project Code";
            $type = ($validate["group_type"] === 'Department') ? "department" : "projects";
            $givenId = ($validate["group_type"] === 'Department') ? $validate["department_id"] : $validate["project_id"];
            $data = Employee::isActive()->with(
                "current_employment",
                "present_address",
                "permanent_address",
                "father",
                "mother",
                "spouse",
                "child",
                "contact_person",
                "employee_education_elementary",
                "employee_education_secondary",
                "employee_education_college",
                "company_employments"
            )->whereHas("current_employment", function ($query) use ($workLocation, $type, $givenId) {
                $query->where('work_location', $workLocation)->whereHas($type, function ($query) use ($type, $givenId) {
                    if ($givenId) {
                        if ($type === "department") {
                            $query->where("departments.id", $givenId);
                        }
                        if ($type === "projects") {
                            $query->where("projects.id", $givenId);
                        }
                    }
                });
            })
                ->get();
        }
        return AdministrativeEmployeeMasterList::collection($data);
    }

    public static function employeeMasterListExport($validate)
    {
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
            'Blood Type',
            'Gender',
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
            'Current Employment Status',
            'Current Position',
            'Salary Grade'
        ];
        $fileName = "storage/temp-report-generations/Masterlist-" . Str::random(10);
        $excel = SimpleExcelWriter::create($fileName . ".xlsx");
        $excel->addHeader($masterListHeaders);
        $reportData = ReportService::employeeMasterList($validate)->resolve();
        foreach ($reportData as $row) {
            $excel->addRow($row);
        }
        $excel->close();
        Storage::disk('public')->delete($fileName . '.xlsx', now()->addMinutes(5));
        return '/' . $fileName . '.xlsx';
    }

    public static function overtimeListExport($validate)
    {
        $masterListHeaders = [
            'Employee Name',
            'Designation',
            'Section',
            'Date of Overtime',
            'Prepared By',
            'Request Status',
            'No. of days delayed filling',
            'Date Approved',
            'Approvals',
        ];
        $fileName = "storage/temp-report-generations/PortalMonitoringOvertimeList-" . Str::random(10);
        $excel = SimpleExcelWriter::create($fileName . ".xlsx");
        $excel->addHeader($masterListHeaders);
        $reportData = ReportService::overtimeMonitoring($validate)->resolve();
        foreach ($reportData as $row) {
            $excel->addRow($row);
        }
        $excel->close();
        Storage::disk('public')->delete($fileName . '.xlsx', now()->addMinutes(5));
        return '/' . $fileName . '.xlsx';
    }

    public static function failureToLogListExport($validate)
    {
        $masterListHeaders = [
            'Employee Name',
            'Designation',
            'Section',
            'Date of Failure to Logs',
            'Date Filled',
            'Prepared By',
            'Request Status',
            'No. of days delayed filling',
            'Date Approved',
            'Approvals',
        ];
        $fileName = "storage/temp-report-generations/PortalMonitoringFailureToLogList-" . Str::random(10);
        $excel = SimpleExcelWriter::create($fileName . ".xlsx");
        $excel->addHeader($masterListHeaders);
        $reportData = ReportService::failureToLogMonitoring($validate)->resolve();
        foreach ($reportData as $row) {
            $excel->addRow($row);
        }
        $excel->close();
        Storage::disk('public')->delete($fileName . '.xlsx', now()->addMinutes(5));
        return '/' . $fileName . '.xlsx';
    }


    public static function salaryListExport($validate)
    {
        $masterListHeaders = [
            'Project Name',
            'Project Identifier',
            'Basic Pay',
            'Number Of Personnel (Basic Pay Charged)',
            'Overtime Pay',
            'Number Of Personnel (Overtime Pay Charged)',
            'Sunday Pay',
            'Number Of Personnel (Sunday Pay Charged)',
            'Allowance',
            'Number Of Personnel (Allowance Charged)',
            'Special Holiday',
            'Number Of Personnel (Special Holiday Pay Charged)',
            'Regular Holiday',
            'Number Of Personnel (Regular Holiday Pay Charged)',
        ];
        $fileName = "storage/temp-report-generations/PortalMonitoringSalaryList-" . Str::random(10);
        $excel = SimpleExcelWriter::create($fileName . ".xlsx");
        $excel->addHeader($masterListHeaders);
        $reportData = ReportService::salaryMonitoring($validate)->resolve();
        foreach ($reportData as $index => $row) {
            $excel->addRow($row);
        }
        $lastIndex = count($reportData);
        $excel->addRow([]);
        $lastIndex += 1;
        $excel->addRow([
            "Total Amount",
            "",
            "=SUM(C2:C{$lastIndex})",
            "",
            "=SUM(E2:E{$lastIndex})",
            "",
            "=SUM(G2:G{$lastIndex})",
            "",
            "=SUM(I2:I{$lastIndex})",
            "",
            "=SUM(K2:K{$lastIndex})",
            "",
            "=SUM(M2:M{$lastIndex})",
            ""
        ]);
        $lastIndex = $lastIndex + 2;
        $excel->addRow([
            "Grand Total Amount",
            ...array_fill(0, 13, ""),
            "=SUM(C{$lastIndex}:N{$lastIndex})"
        ]);
        $excel->close();
        Storage::disk('public')->delete($fileName . '.xlsx', now()->addMinutes(5));
        return '/' . $fileName . '.xlsx';
    }

    public static function overtimeSummaryListExport($validate)
    {
        $masterListHeaders = [
            'Employee Name',
            'Total Number of Overtime Filled',
        ];
        $fileName = "storage/temp-report-generations/PortalMonitoringOvertimeSummaryList-" . Str::random(10);
        $excel = SimpleExcelWriter::create($fileName . ".xlsx");
        $excel->addHeader($masterListHeaders);
        $reportData = ReportService::overtimeSummaryMonitoring($validate)->resolve();
        foreach ($reportData as $row) {
            $excel->addRow($row);
        }
        $excel->close();
        Storage::disk('public')->delete($fileName . '.xlsx', now()->addMinutes(5));
        return '/' . $fileName . '.xlsx';
    }
    public static function failureToLogMonitoringSummaryListExport($validate)
    {
        $masterListHeaders = [
            'Employee Name',
            'Total Number of Failure to Log Filed',
        ];
        $fileName = "storage/temp-report-generations/PortalMonitoringFailureToLogMonitoringSummaryList-" . Str::random(10);
        $excel = SimpleExcelWriter::create($fileName . ".xlsx");
        $excel->addHeader($masterListHeaders);
        $reportData = ReportService::failureToLogMonitoringSummary($validate)->resolve();
        foreach ($reportData as $row) {
            $excel->addRow($row);
        }
        $excel->close();
        Storage::disk('public')->delete($fileName . '.xlsx', now()->addMinutes(5));
        return '/' . $fileName . '.xlsx';
    }

    public static function employeeNewList($validate)
    {
        $data = Employee::isActive()->with("current_employment", "company_employments")->whereHas(
            'company_employments',
            function ($query) use ($validate) {
                $query->whereBetween('date_hired', [$validate["date_from"], $validate["date_to"]]);
            }
        )->get();
        if ($validate["group_type"] != "All") {
            $workLocation = ($validate["group_type"] === 'Department') ? "Office" : "Project Code";
            $type = ($validate["group_type"] === 'Department') ? "department" : "projects";
            $givenId = ($validate["group_type"] === 'Department') ? $validate["department_id"] : $validate["project_id"];

            $data = Employee::isActive()->with("current_employment", "company_employments")->whereHas(
                'company_employments',
                function ($query) use ($validate) {
                    $query->whereBetween('date_hired', [$validate["date_from"], $validate["date_to"]]);
                }
            )->whereHas("current_employment", function ($query) use ($workLocation, $type, $givenId) {
                $query->where('work_location', $workLocation)
                    ->whereHas($type, function ($query) use ($type, $givenId) {
                        if ($givenId) {
                            if ($type === "department") {
                                $query->where("departments.id", $givenId);
                            }
                            if ($type === "projects") {
                                $query->where("projects.id", $givenId);
                            }
                        }
                    });
            })->whereHas('employee_leave', function ($query) use ($validate) {
                $query->betweenDates($validate["date_from"], $validate["date_to"]);
            })->get();
        }
        return AdministrativeEmployeeNewList::collection($data);
    }

    public static function leaveMonitoringExport($validate)
    {
        $masterListHeaders = [
            'Employee Name',
            'Designation',
            'Section',
            'Date of Leave',
            'Date Filled',
            'Prepared By',
            'Request Status',
            'No. of days delayed filling',
            'Date Approved',
            'Approvals',
        ];
        $fileName = "storage/temp-report-generations/PortalMonitoringLeaveList-" . Str::random(10);
        $excel = SimpleExcelWriter::create($fileName . ".xlsx");
        $excel->addHeader($masterListHeaders);
        $reportData = ReportService::leaveMonitoring($validate)->resolve();
        foreach ($reportData as $row) {
            $excel->addRow($row);
        }
        $excel->close();
        Storage::disk('public')->delete($fileName . '.xlsx', now()->addMinutes(5));
        return '/' . $fileName . '.xlsx';
    }

    public static function leaveMonitoringSummaryExport($validate)
    {
        $masterListHeaders = [
            'Employee Name',
            'Total Number of Leave Filed',
        ];
        $fileName = "storage/temp-report-generations/PortalMonitoringLeaveSummaryList-" . Str::random(10);
        $excel = SimpleExcelWriter::create($fileName . ".xlsx");
        $excel->addHeader($masterListHeaders);
        $reportData = ReportService::leaveMonitoringSummary($validate)->resolve();
        foreach ($reportData as $row) {
            $excel->addRow($row);
        }
        $excel->close();
        Storage::disk('public')->delete($fileName . '.xlsx', now()->addMinutes(5));
        return '/' . $fileName . '.xlsx';
    }

    public static function travelOrderMonitoringExport($validate)
    {
        $masterListHeaders = [
            'Employee Name',
            'Designation',
            'Section',
            'Date of Travel Order',
            'Date Filled',
            'Prepared By',
            'Request Status',
            'No. of days delayed filling',
            'Date Approved',
            'Approvals',
        ];
        $fileName = "storage/temp-report-generations/PortalMonitoringTravelOrderList-" . Str::random(10);
        $excel = SimpleExcelWriter::create($fileName . ".xlsx");
        $excel->addHeader($masterListHeaders);
        $reportData = ReportService::travelOrderMonitoring($validate);
        foreach ($reportData as $row) {
            $excel->addRow($row);
        }
        $excel->close();
        Storage::disk('public')->delete($fileName . '.xlsx', now()->addMinutes(5));
        return '/' . $fileName . '.xlsx';
    }

    public static function travelOrderMonitoringSummaryExport($validate)
    {
        $masterListHeaders = [
            'Employee Name',
            'Total Number of Travel Order Filed',
        ];
        $fileName = "storage/temp-report-generations/PortalMonitoringTravelOrderSummaryList-" . Str::random(10);
        $excel = SimpleExcelWriter::create($fileName . ".xlsx");
        $excel->addHeader($masterListHeaders);
        $reportData = ReportService::travelOrderMonitoringSummary($validate)->resolve();
        foreach ($reportData as $row) {
            $excel->addRow($row);
        }
        $excel->close();
        Storage::disk('public')->delete($fileName . '.xlsx', now()->addMinutes(5));
        return '/' . $fileName . '.xlsx';
    }

    public static function manpowerRequestMonitoringExport($validate)
    {
        $masterListHeaders = [
            'Requesting Department',
            'Requested Position/Title',
            'Employment Type',
            'Nature of Request',
            'Age range',
            'Civil Status',
            'Gender',
            'Education Requirement',
            'Preffered Qualification',
            'Date Required',
            'Date Requested',
            'Requested by',
            'Request Status',
            'No. of Days Delayed Filing',
            'Date Approved',
            'Approvals',
        ];
        $fileName = "storage/temp-report-generations/PortalMonitoringManpowerRequestList-" . Str::random(10);
        $excel = SimpleExcelWriter::create($fileName . ".xlsx");
        $excel->addHeader($masterListHeaders);
        $reportData = ReportService::manpowerRequestMonitoring($validate)->resolve();
        foreach ($reportData as $row) {
            $excel->addRow($row);
        }
        $excel->close();
        Storage::disk('public')->delete($fileName . '.xlsx', now()->addMinutes(5));
        return '/' . $fileName . '.xlsx';
    }

    public static function panTransferMonitoringExport($validate)
    {
        $masterListHeaders = [
            'Employee Name',
            'Current Work Location',
            'Current Salary Type',
            'Old Position',
            'New Work Location',
            'New Salary Type',
            'New Position',
            'Effectivity Date',
            'Requested by',
            'Request Status',
            'No. of Days Delayed Filing',
            'Date Approved',
            'Approvals',
        ];
        $fileName = "storage/temp-report-generations/PortalMonitoringPanTransferList-" . Str::random(10);
        $excel = SimpleExcelWriter::create($fileName . ".xlsx");
        $excel->addHeader($masterListHeaders);
        $reportData = ReportService::panTransferMonitoring($validate)->resolve();
        foreach ($reportData as $row) {
            $excel->addRow($row);
        }
        $excel->close();
        Storage::disk('public')->delete($fileName . '.xlsx', now()->addMinutes(5));
        return '/' . $fileName . '.xlsx';
    }

    public static function manpowerRequestMonitoringSummaryExport($validate)
    {
        $masterListHeaders = [
            'Requested Position/Title',
            'Total Number Requested',
            'Total Number of Unserved',
            'Total Number of Served',
        ];
        $fileName = "storage/temp-report-generations/PortalMonitoringManpowerRequestSummaryList-" . Str::random(10);
        $excel = SimpleExcelWriter::create($fileName . ".xlsx");
        $excel->addHeader($masterListHeaders);
        $reportData = ReportService::manpowerRequestMonitoringSummary($validate)->resolve();
        foreach ($reportData as $row) {
            $excel->addRow($row);
        }
        $excel->close();
        Storage::disk('public')->delete($fileName . '.xlsx', now()->addMinutes(5));
        return '/' . $fileName . '.xlsx';
    }

    public static function employeeLeaves($validate)
    {
        $data = Employee::isActive()->with([
            "company_employments",
            "current_employment",
            'employee_leave' => function ($query) use ($validate) {
                $query->betweenDates($validate["date_from"], $validate["date_to"]);
            }
        ])->whereHas('employee_leave', function ($query) use ($validate) {
            $query->betweenDates($validate["date_from"], $validate["date_to"]);
        })->get();

        if ($validate["group_type"] != "All") {
            $workLocation = ($validate["group_type"] === 'Department') ? "Office" : "Project Code";
            $type = ($validate["group_type"] === 'Department') ? "department" : "projects";
            $givenId = ($validate["group_type"] === 'Department') ? $validate["department_id"] : $validate["project_id"];

            $data = Employee::isActive()->with([
                "company_employments",
                "current_employment",
                'employee_leave' => function ($query) use ($validate) {
                    $query->betweenDates($validate["date_from"], $validate["date_to"]);
                }
            ])->whereHas("current_employment", function ($query) use ($workLocation, $type, $givenId) {
                $query->where('work_location', $workLocation)
                    ->whereHas($type, function ($query) use ($type, $givenId) {
                        if ($givenId) {
                            if ($type === "department") {
                                $query->where("departments.id", $givenId);
                            }
                            if ($type === "projects") {
                                $query->where("projects.id", $givenId);
                            }
                        }
                    });
            })->whereHas('employee_leave', function ($query) use ($validate) {
                $query->betweenDates($validate["date_from"], $validate["date_to"]);
            })->get();
        }

        return AdministrativeEmployeeLeaves::collection($data);
    }

    public static function employeeAbsences($validate)
    {
        $dateFrom = Carbon::parse($validate["date_from"]);
        $dateTo = Carbon::parse($validate["date_to"]);
        $employeeDtr = AttendanceReportService::getEmployeeDtr($dateFrom, $dateTo, $validate);
        $events = AttendanceReportService::getEvents($dateFrom, $dateTo);
        $reportData = $employeeDtr->map(function ($employee) use ($dateFrom, $dateTo, $events) {
            $employeeDatas = [
                "employee" => $employee,
                "internals" => $employee->employee_internal,
                "employee_schedules_irregular" => $employee->employee_schedule_irregular,
                "employee_schedules_regular" => $employee->employee_schedule_regular,
                // PROJECT SCHEDULES TO BE TAKEN FROM employee->"employee_internal->projects"
                // DEPARTMENT SCHEDULE TO BE TAKEN FROM employee->"employee_internal->department"
                "overtimes" => $employee->employee_overtime,
                "attendanceLogs" => $employee->attendance_log,
                "travel_orders" => $employee->employee_travel_order,
                "leaves" => $employee->employee_leave,
                'events' => $events,
            ];
            $dtr = AttendanceReportService::processEmployeeDtr($employeeDatas, $dateFrom, $dateTo);
            $employeeAttendance = AttendanceReportService::employeeAttendance($dtr, $events);
            return [
                "employee_name" => $employee->fullname_last,
                "fullname_first" => $employee->fullname_first,
                "employee_id" => $employee->company_employments?->employeedisplay_id,
                "designation" => $employee->current_position_name,
                "section" => $employee->current_assignment_names,
                "total_absents" => $employeeAttendance["absenceCount"],
                "total_attendance" => $employeeAttendance["attendanceCount"],
                "total_lates" => $employeeAttendance["lateCount"],
                "profile_photo" => $employee->profile_photo ? new CompressedImageResource($employee->profile_photo) : null,
            ];
        });
        return $reportData;
    }

    public static function overtimeMonitoring($validate)
    {
        $allData = [];
        $dateFrom = Carbon::parse($validate["date_from"]);
        $dateTo = Carbon::parse($validate["date_to"]);
        $withDepartment = $validate["group_type"] == GroupType::DEPARTMENT->value;
        $withProject = $validate["group_type"] == GroupType::PROJECT->value;
        $main = Overtime::with("employees")
            ->betweenDates($dateFrom, $dateTo)
            ->when($withDepartment, function ($query) use ($validate) {
                return $query->where('department_id', $validate['department_id']);
            })
            ->when($withProject, function ($query) use ($validate) {
                return $query->where('project_id', $validate['project_id']);
            })->get();

        $returnData = PortalMonitoringOvertime::collection($main);
        return $returnData;
    }

    public static function salaryMonitoring($validate)
    {
        $dateFrom = Carbon::parse($validate["date_from"]);
        $dateTo = Carbon::parse($validate["date_to"]);
        $withDepartment = $validate["group_type"] == GroupType::DEPARTMENT->value;
        $withProject = $validate["group_type"] == GroupType::PROJECT->value;
        $payrollRecords = PayrollRecord::isApproved()->betweenDates($dateFrom, $dateTo)->get();
        $allowanceRequest =  AllowanceRequest::isApproved()
            ->with("employee_allowances")
            ->betweenDates($dateFrom, $dateTo)
            ->when($withDepartment, function ($query) {
                return $query->where('charge_assignment_type', SalaryMonitoringReportService::DEPARTMENT);
            })
            ->when($withProject, function ($query) {
                return $query->where('charge_assignment_type', SalaryMonitoringReportService::PROJECT);
            })->get();
        $payrollRecordsIds = $payrollRecords->pluck("id");
        $payrollSummaryDatas = SalaryMonitoringReportService::getPayrollSummary($payrollRecordsIds, $allowanceRequest, $withDepartment, $withProject);

        return PortalMonitoringSalary::collection($payrollSummaryDatas);
    }

    public static function overtimeSummaryMonitoring($validate)
    {
        $withDepartment = $validate["group_type"] == GroupType::DEPARTMENT->value;
        $withProject = $validate["group_type"] == GroupType::PROJECT->value;
        $dateFrom = Carbon::parse($validate["date_from"]);
        $dateTo = Carbon::parse($validate["date_to"]);
        $main = Employee::isActive()->with("employee_overtime")
            ->whereHas('employee_overtime', function ($query) use ($validate, $withDepartment, $withProject, $dateFrom, $dateTo) {
                $query->isApproved()
                    ->betweenDates($dateFrom, $dateTo)
                    ->when($withDepartment, function ($query) use ($validate) {
                        return $query->where('department_id', $validate['department_id']);
                    })
                    ->when($withProject, function ($query) use ($validate) {
                        return $query->where('project_id', $validate['project_id']);
                    });
            })->get();

        $formatData = collect($main)->map(function ($item) use ($dateFrom, $dateTo) {
            $uniqueOvertimeIds = collect($item['employee_overtime'])->filter(function ($overtime) use ($dateFrom, $dateTo) {
                return $overtime['overtime_date'] >= $dateFrom && $overtime['overtime_date'] <= $dateTo;
            })->pluck('id')->unique();
            $item['total_filled_overtime'] = $uniqueOvertimeIds->count();
            return $item;
        });

        $returnData = PortalMonitoringOvertimeSummary::collection($formatData);
        return $returnData;
    }

    public static function failureToLogMonitoringSummary($validate)
    {
        $withDepartment = $validate["group_type"] == GroupType::DEPARTMENT->value;
        $withProject = $validate["group_type"] == GroupType::PROJECT->value;
        $dateFrom = Carbon::parse($validate["date_from"]);
        $dateTo = Carbon::parse($validate["date_to"]);
        $main = Employee::isActive()->with("employee_failure_to_log")
            ->whereHas('employee_failure_to_log', function ($query) use ($validate, $withDepartment, $withProject, $dateFrom, $dateTo) {
                $query->isApproved()
                    ->betweenDates($dateFrom, $dateTo)
                    ->when($withDepartment, function ($query) use ($validate) {
                        return $query->where('charging_type', FailureToLog::DEPARTMENT)
                            ->where('charging_id', $validate['department_id']);
                    })
                    ->when($withProject, function ($query) use ($validate) {
                        return $query->where('charging_type', FailureToLog::PROJECT)
                            ->where('charging_id', $validate['project_id']);
                    });
            })->get();

        $formatData = collect($main)->map(function ($item) use ($dateFrom, $dateTo) {
            $uniqueOvertimeIds = collect($item['employee_failure_to_log'])->filter(function ($overtime) use ($dateFrom, $dateTo) {
                return $overtime['date'] >= $dateFrom && $overtime['date'] <= $dateTo;
            })->pluck('id')->unique();
            $item['total_filed_failuretolog'] = $uniqueOvertimeIds->count();
            return $item;
        });

        $returnData = PortalMonitoringFailureToLogSummary::collection($formatData);
        return $returnData;
    }

    public static function failureToLogMonitoring($validate)
    {
        $withDepartment = $validate["group_type"] == GroupType::DEPARTMENT->value;
        $withProject = $validate["group_type"] == GroupType::PROJECT->value;
        $main = FailureToLog::isApproved()
            ->with("employee")
            ->whereHas('employee', function ($query) use ($validate, $withDepartment, $withProject) {
                $query->isActive();
            })->betweenDates($validate["date_from"], $validate["date_to"])
            ->when($withDepartment, function ($query) use ($validate) {
                return $query->where('charging_type', FailureToLog::DEPARTMENT)
                    ->where('charging_id', $validate['department_id']);
            })
            ->when($withProject, function ($query) use ($validate) {
                return $query->where('charging_type', FailureToLog::PROJECT)
                    ->where('charging_id', $validate['project_id']);
            })->get();
        $returnData = PortalMonitoringFailureToLog::collection($main);
        return $returnData;
    }

    public static function leaveMonitoring($validate)
    {
        $withDepartment = $validate["group_type"] == GroupType::DEPARTMENT->value;
        $withProject = $validate["group_type"] == GroupType::PROJECT->value;
        $main = EmployeeLeaves::isApproved()
            ->with("employee")
            ->whereHas('employee', function ($query) {
                $query->isActive();
            })->betweenDates($validate["date_from"], $validate["date_to"])
            ->when($withDepartment, function ($query) use ($validate) {
                return $query->where('department_id', $validate['department_id']);
            })
            ->when($withProject, function ($query) use ($validate) {
                return $query->where('project_id', $validate['project_id']);
            })->get();
        $returnData = PortalMonitoringLeave::collection($main);
        return $returnData;
    }

    public static function leaveMonitoringSummary($validate)
    {
        $withDepartment = $validate["group_type"] == GroupType::DEPARTMENT->value;
        $withProject = $validate["group_type"] == GroupType::PROJECT->value;
        $dateFrom = Carbon::parse($validate["date_from"]);
        $dateTo = Carbon::parse($validate["date_to"]);
        $main = Employee::isActive()->with("employee_leave")
            ->whereHas('employee_leave', function ($query) use ($validate, $withDepartment, $withProject, $dateFrom, $dateTo) {
                $query->isApproved()
                    ->betweenDates($dateFrom, $dateTo)
                    ->when($withDepartment, function ($query) use ($validate) {
                        return $query->where('department_id', $validate['department_id']);
                    })
                    ->when($withProject, function ($query) use ($validate) {
                        return $query->where('project_id', $validate['project_id']);
                    });
            })->get();

        $formatData = collect($main)->map(function ($item) {
            $uniqueOvertimeIds = collect($item['employee_leave'])->pluck('id')->unique();
            $item['total_leave_filed'] = $uniqueOvertimeIds->count();
            return $item['total_leave_filed'] > 0 ? $item : null;
        })->filter()->values();

        $returnData = PortalMonitoringLeaveSummary::collection($formatData);
        return $returnData;
    }

    public static function travelOrderMonitoring($validate)
    {
        $withDepartment = $validate["group_type"] == GroupType::DEPARTMENT->value;
        $withProject = $validate["group_type"] == GroupType::PROJECT->value;
        $dateFrom = Carbon::parse($validate["date_from"]);
        $dateTo = Carbon::parse($validate["date_to"]);
        $main = TravelOrder::isApproved()
            ->with("employees")
            ->whereHas('employees', function ($query) {
                $query->isActive();
            })->betweenDates($dateFrom, $dateTo)
            ->when($withDepartment, function ($query) use ($validate) {
                return $query->where('charge_type', TravelOrder::DEPARTMENT)
                    ->where('charge_id', $validate['department_id']);
            })
            ->when($withProject, function ($query) use ($validate) {
                return $query->where('charge_type', TravelOrder::PROJECT)
                    ->where('charge_id', $validate['project_id']);
            })->get();
        $returnData = PortalMonitoringTravelOrder::collection($main);
        $flattenedData = collect($returnData)->flatMap(fn ($group) => collect($group))->values();
        return $flattenedData;
    }

    public static function travelOrderMonitoringSummary($validate)
    {
        $withDepartment = $validate["group_type"] == GroupType::DEPARTMENT->value;
        $withProject = $validate["group_type"] == GroupType::PROJECT->value;
        $dateFrom = Carbon::parse($validate["date_from"]);
        $dateTo = Carbon::parse($validate["date_to"]);
        $main = Employee::isActive()->with("employee_travel_order")
            ->whereHas('employee_travel_order', function ($query) use ($validate, $withDepartment, $withProject, $dateFrom, $dateTo) {
                $query->betweenDates($dateFrom, $dateTo)
                    ->when($withDepartment, function ($query) use ($validate) {
                        return $query->where('charge_type', TravelOrder::DEPARTMENT)
                        ->where('charge_id', $validate['department_id']);
                    })
                    ->when($withProject, function ($query) use ($validate) {
                        return $query->where('charge_type', TravelOrder::PROJECT)
                        ->where('charge_id', $validate['project_id']);
                    });
            })->get();

        $updatedData = collect($main)->map(function ($item) use ($dateFrom, $dateTo) {
            $filteredOrders = collect($item['employee_travel_order'])->whereBetween('date_of_travel', [$dateFrom, $dateTo])->values();
            return [
                'id' => $item['id'],
                'fullname_last' => $item['fullname_last'],
                'filter_employee_travel_order' => $filteredOrders->all(),
                'total_travel_order' => $filteredOrders->count(),
            ];
        })->toArray();

        $returnData = PortalMonitoringTravelOrderSummary::collection($updatedData);
        return $returnData;
    }

    public static function manpowerRequestMonitoring($validate)
    {
        $withDepartment = $validate["group_type"] == GroupType::DEPARTMENT->value;
        $withProject = $validate["group_type"] == GroupType::PROJECT->value;
        if ($withProject) {
            return collect([]);
        }
        $main = ManpowerRequest::isApproved()
            ->with("department", "position")
            ->betweenDates($validate["date_from"], $validate["date_to"])
            ->when($withDepartment, function ($query) use ($validate) {
                return $query->where('requesting_department', $validate['department_id']);
            })->get();

        $returnData = PortalMonitoringManpowerRequest::collection($main);
        return $returnData;
    }

    public static function manpowerRequestMonitoringSummary($validate)
    {
        $withDepartment = $validate["group_type"] == GroupType::DEPARTMENT->value;
        $withProject = $validate["group_type"] == GroupType::PROJECT->value;
        if ($withProject) {
            return collect([]);
        }
        $main = ManpowerRequest::isApproved()
            ->with("department", "position", "manpowerRequestJobApplicants")
            ->betweenDates($validate["date_from"], $validate["date_to"])
            ->when($withDepartment, function ($query) use ($validate) {
                return $query->where('requesting_department', $validate['department_id']);
            })->get();

        $dataMerge = $main->groupBy('position.name')->map(function ($entries) {
            return [
                "name" => $entries->first()["position"]["name"],
                "total_number_requested" => $entries->flatMap(fn ($entry) => $entry["manpowerRequestJobApplicants"])->count(),
                "total_number_unserved" => $entries->sum(fn ($entry) => collect($entry["manpowerRequestJobApplicants"])
                ->filter(fn ($applicant) => in_array($applicant["hiring_status"], ["Processing", "For Hiring"]))
                ->count()),
                "total_number_served" => $entries->sum(fn ($entry) => collect($entry["manpowerRequestJobApplicants"])
                ->filter(fn ($applicant) => $applicant["hiring_status"] === "Hired")
                ->count()),
                "manpower_request_job_applicants" => $entries->flatMap(fn ($entry) => $entry["manpowerRequestJobApplicants"])->map(fn ($request) => [
                    "id" => $request["id"],
                    "job_applicants_id" => $request["job_applicants_id"],
                    "manpowerrequests_id" => $request["manpowerrequests_id"],
                    "hiring_status" => $request["hiring_status"]
                ])
            ];
        })->values();
        $returnData = PortalMonitoringManpowerRequestSummary::collection($dataMerge);
        return $returnData;
    }

    public static function panTerminationMonitoring($validate)
    {
        $withDepartment = $validate["group_type"] == GroupType::DEPARTMENT->value;
        $withProject = $validate["group_type"] == GroupType::PROJECT->value;
        $main = EmployeePanRequest::isApproved()
            ->with("employee", "projects", "department", "position")
            ->where("type", PanRequestType::TERMINATION)
            ->whereHas('employee', function ($query) {
                $query->isActive();
            })
            ->betweenDates($validate["date_from"], $validate["date_to"])
            ->when($withDepartment, function ($query) use ($validate) {
                return $query->has('department')->whereHas('department', function ($withQuery) use ($validate) {
                    if (!isset($validate['department_id']) || is_null($validate['department_id'])) {
                        return $withQuery;
                    }
                    $withQuery->where('id', $validate['department_id']);
                });
            })
            ->when($withProject, function ($query) use ($validate) {
                return $query->has('projects')->whereHas('projects', function ($withQuery) use ($validate) {
                    if (!isset($validate['project_id']) || is_null($validate['project_id'])) {
                        return $withQuery;
                    }
                    $withQuery->where('projects.id', $validate['project_id']);
                });
            })
            ->get();

        $returnData = PortalMonitoringPanTermination::collection($main);
        return $returnData;
    }

    public static function panTransferMonitoring($validate)
    {
        $withDepartment = $validate["group_type"] == GroupType::DEPARTMENT->value;
        $withProject = $validate["group_type"] == GroupType::PROJECT->value;
        $main = EmployeePanRequest::isApproved()
            ->with("employee", "projects", "department", "position")
            ->where("type", PanRequestType::TRANSFER)
            ->whereHas('employee', function ($query) {
                $query->isActive();
            })
            ->betweenDates($validate["date_from"], $validate["date_to"])
            ->when($withDepartment, function ($query) use ($validate) {
                return $query->has('department')->whereHas('department', function ($withQuery) use ($validate) {
                    if (!isset($validate['department_id']) || is_null($validate['department_id'])) {
                        return $withQuery;
                    }
                    $withQuery->where('id', $validate['department_id']);
                });
            })
            ->when($withProject, function ($query) use ($validate) {
                return $query->has('projects')->whereHas('projects', function ($withQuery) use ($validate) {
                    if (!isset($validate['project_id']) || is_null($validate['project_id'])) {
                        return $withQuery;
                    }
                    $withQuery->where('projects.id', $validate['project_id']);
                });
            })
            ->get();

        $returnData = PortalMonitoringPanTransfer::collection($main);
        return $returnData;
    }

    public static function panTerminationMonitoringExport($validate)
    {
        $masterListHeaders = [
            'Employee Name',
            'Designation',
            'Section',
            'Last Day Worked',
            'Termination Type',
            'Termination Reason',
            'Eligible for re-hire',
            'Effectivity Date',
            'Date Requested',
            'Requested by',
            'Request Status',
            'No. of Days Delayed Filing',
            'Date Approved',
            'Approvals',
        ];
        $fileName = "storage/temp-report-generations/PortalMonitoringPanTerminationList-" . Str::random(10);
        $excel = SimpleExcelWriter::create($fileName . ".xlsx");
        $excel->addHeader($masterListHeaders);
        $reportData = ReportService::panTerminationMonitoring($validate)->resolve();
        foreach ($reportData as $row) {
            $excel->addRow($row);
        }
        $excel->close();
        Storage::disk('public')->delete($fileName . '.xlsx', now()->addMinutes(5));
        return '/' . $fileName . '.xlsx';
    }

    public static function panPromotionMonitoring($validate)
    {
        $withDepartment = $validate["group_type"] == GroupType::DEPARTMENT->value;
        $withProject = $validate["group_type"] == GroupType::PROJECT->value;
        $main = EmployeePanRequest::isApproved()
            ->with("employee", "projects", "department", "position", "salarygrade")
            ->where("type", PanRequestType::PROMOTION)
            ->whereHas('employee', function ($query) {
                $query->isActive();
            })
            ->betweenDates($validate["date_from"], $validate["date_to"])
            ->when($withDepartment, function ($query) use ($validate) {
                return $query->has('department')->whereHas('department', function ($withQuery) use ($validate) {
                    if (!isset($validate['department_id']) || is_null($validate['department_id'])) {
                        return $withQuery;
                    }
                    $withQuery->where('id', $validate['department_id']);
                });
            })
            ->when($withProject, function ($query) use ($validate) {
                return $query->has('projects')->whereHas('projects', function ($withQuery) use ($validate) {
                    if (!isset($validate['project_id']) || is_null($validate['project_id'])) {
                        return $withQuery;
                    }
                    $withQuery->where('projects.id', $validate['project_id']);
                });
            })
            ->get();

        $returnData = PortalMonitoringPanPromotion::collection($main);
        return $returnData;
    }
}

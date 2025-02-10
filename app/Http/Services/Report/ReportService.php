<?php

namespace App\Http\Services\Report;

use App\Enums\Reports\LoanReports;
use App\Enums\GroupType;
use App\Http\Services\Report\AttendanceReportService;
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
use App\Models\Loans;
use App\Models\OtherDeduction;
use App\Models\PayrollDetail;
use App\Models\Employee;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Helpers;

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
        return[
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
                "total_group_amount" => $employeeData->sum(function($detail) use ($validatedData) {
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
                "percov" => $validatedData['filter_month'].$validatedData['filter_year'],
                "total_payments" => $employeeData->sum(function($detail) use ($validatedData) {
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
                "total_payments" => $employeeData->sum(function($detail) use ($validatedData) {
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
                "percov" => $validatedData['filter_month'].$validatedData['filter_year'],
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
                "percov" => $validatedData['filter_month'].$validatedData['filter_year'],
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
                "total_group_amount" => $employeeData->sum(function($detail) use ($validatedData) {
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
                "percov" => $validatedData['filter_month'].$validatedData['filter_year'],
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
                "percov" => $validatedData['filter_month'].$validatedData['filter_year'],
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
            'loanPayments' => function($query) use($validatedData) {
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
            $totalLoanPayments = $employeeData->sum(function($detail) use($validatedData) {
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
                "percov" => $validatedData['filter_month'].$validatedData['filter_year'],
            ];
        })
        ->values()
        ->all();
    }
    public static function getLoanGroupReport($validatedData)
    {
        $data = PayrollDetail::with(["payroll_record", "employee.company_employments"])
        ->with([
            'loanPayments' => function($query) use($validatedData) {
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
                "percov" => $validatedData['filter_month'].$validatedData['filter_year'],
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
            'otherDeductionPayments' => function($query) use($validatedData) {
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
            $totalLoanPayments = $employeeData->sum(function($detail) use($validatedData) {
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
                "percov" => $validatedData['filter_month'].$validatedData['filter_year'],
            ];
        })
        ->values()
        ->all();
    }
    public static function getOtherDeductionGroupReport($validatedData)
    {
        $data = PayrollDetail::with(["payroll_record", "employee.company_employments"])
        ->with([
            'otherDeductionPayments' => function($query) use($validatedData) {
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
                "percov" => $validatedData['filter_month'].$validatedData['filter_year'],
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
        if($validate["group_type"]!==GroupType::ALL->value){
            $workLocation = ($validate["group_type"] === 'Department') ? "Office" : "Project Code";
            $type = ($validate["group_type"] === 'Department') ? "department" : "projects";
            $givenId = ($validate["group_type"] === 'Department') ? $validate["department_id"] : $validate["project_id"];
            $data = Employee::isActive()->with("current_employment")->whereHas("current_employment",
                function ($query) use ($workLocation, $type, $givenId) {
                    $query->where('work_location', $workLocation)->whereHas($type,
                        function ($query) use ($type, $givenId) {
                            if($givenId) {
                                if($type === "department"){
                                    $query->where("departments.id", $givenId);
                                }
                                if($type === "projects"){
                                    $query->where("projects.id", $givenId);
                                }
                            }
                        }
                    );
                })
            ->get();
        }
        return AdministrativeEmployeeTenureship::collection($data);
    }

    public static function employeeMasterList($validate)
    {
        $data = Employee::isActive()->with("current_employment","present_address","permanent_address", "father", "mother", "spouse", "child",
        "contact_person", "employee_education_elementary", "employee_education_secondary", "employee_education_college", "company_employments")->get();
        if($validate["group_type"]!==GroupType::ALL->value){
            $workLocation = ($validate["group_type"] === 'Department') ? "Office" : "Project Code";
            $type = ($validate["group_type"] === 'Department') ? "department" : "projects";
            $givenId = ($validate["group_type"] === 'Department') ? $validate["department_id"] : $validate["project_id"];
            $data = Employee::isActive()->with("current_employment","present_address","permanent_address", "father", "mother", "spouse", "child",
            "contact_person", "employee_education_elementary", "employee_education_secondary", "employee_education_college", "company_employments")->whereHas("current_employment",
                function ($query) use ($workLocation, $type, $givenId) {
                    $query->where('work_location', $workLocation)->whereHas($type,
                        function ($query) use ($type, $givenId) {
                            if($givenId) {
                                if($type === "department"){
                                    $query->where("departments.id", $givenId);
                                }
                                if($type === "projects"){
                                    $query->where("projects.id", $givenId);
                                }
                            }
                        }
                    );
                })
            ->get();
        }
        return AdministrativeEmployeeMasterList::collection($data);
    }

    public static function employeeMasterListExport($validate)
    {
        $reportData = ReportService::employeeMasterList($validate);
        $formatList = [];
        foreach ($reportData as $row) {
            $dateBirth = $row['date_of_birth'] ? Carbon::parse($row['date_of_birth'])->format('F j, Y') : "Date Birth N/A";
            $spouseDateBirth = $row->spouse?->date_of_birth ? Carbon::parse($row->spouse?->date_of_birth)->format('F j, Y') : "Spouse Date Birth N/A";
            array_push($formatList, [
                $row->company_employments?->employeedisplay_id,
                $row->company_employments?->employee_date_hired,
                $row['first_name'],
                $row['middle_name'],
                $row['family_name'],
                $row['name_suffix'],
                $row['nick_name'],
                $row->present_address?->complete_address,
                $row->permanent_address?->complete_address,
                $row['mobile_number'],
                $dateBirth,
                $row['place_of_birth'],
                $row['citizenship'],
                $row['blood_type'],
                $row['gender'],
                $row['religion'],
                $row['civil_status'],
                $row['height'],
                $row['weight'],
                $row->date_marriage,
                $row->father?->name,
                $row->mother?->name,
                $row->spouse?->name,
                $spouseDateBirth,
                $row->spouse?->name,
                $row->child->pluck('name_bday')->implode(', '),
                $row->contact_person?->name,
                $row->contact_person?->address,
                $row->contact_person?->contact_no,
                $row->contact_person?->relationship,
                $row->employee_education_elementary?->education,
                $row->employee_education_secondary?->education,
                $row->employee_education_college?->education,
                $row->company_employments?->sss_number,
                $row->company_employments?->phic_number,
                $row->company_employments?->pagibig_number,
                $row->company_employments?->tin_number,
                $row->current_assignment_names,
                $row->current_position_name,
                $row->current_salarygrade_and_step,
            ]);
        }
        return $formatList;
    }

    public static function employeeNewList($validate)
    {
        $data = Employee::isActive()->with("current_employment", "company_employments")->whereHas('company_employments',
            function ($query) use ($validate) {
                $query->whereBetween('date_hired', [$validate["date_from"], $validate["date_to"]]);
            }
        )->get();
        return AdministrativeEmployeeNewList::collection($data);
    }
    public static function employeeLeaves($validate)
    {
        $data = Employee::isActive()->with([
            "company_employments",
            "current_employment",
            'employee_leave' => function ($query) use ($validate) {
            $query->betweenDates($validate["date_from"], $validate["date_to"]);
        }])->whereHas('employee_leave', function ($query) use ($validate) {
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
                        if($givenId) {
                            if($type === "department"){
                                $query->where("departments.id", $givenId);
                            }
                            if($type === "projects"){
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
            $periodDates = Helpers::dateRange([
                'period_start' => $dateFrom, 'period_end' => $dateTo
            ]);
            $schedules = collect($periodDates)->groupBy("date")->map(function ($val, $date) use ($employeeDatas) {
                $carbonDate = Carbon::parse($date);
                $appliedDateSchedule = AttendanceReportService::getAppliedDateSchedule($employeeDatas, $carbonDate);
                return $appliedDateSchedule;
            });
            $employeeAttendance = AttendanceReportService::employeeAttendance($employee, $dateFrom, $dateTo, $events, $schedules);
            return [
                "employee_name" => $employee->fullname_last,
                "employee_id" => $employee->company_employments?->employeedisplay_id,
                "designation" => $employee->current_position_name,
                "section" => $employee->current_assignment_names,
                "total_absents" => $employeeAttendance["absenceCount"],
            ];
        });
        return $reportData;
    }
}

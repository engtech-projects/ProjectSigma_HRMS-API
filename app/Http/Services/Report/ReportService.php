<?php

namespace App\Http\Services\Report;

use App\Enums\EmployeeLoanType;
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
use App\Models\PayrollDetail;

class ReportService
{
    public function sssEmployeeRemittance($validatedData = [])
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
    public function sssGroupRemittance($validatedData = [])
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
    public function sssRemittanceSummary($validatedData = [])
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
    public function pagibigEmployeeRemittance($validatedData = [])
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
    public function pagibigGroupRemiitance($validatedData = [])
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
    public function pagibigRemittanceSummary($validatedData = [])
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
        return[
            'success' => true,
            'message' => 'SSS Group Remittance Request fetched.',
            'data' => PagibigRemittanceSummaryResource::collection($uniqueGroup),
        ];
    }
    public function philhealthEmployeeRemittance($validatedData = [])
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
    public function philhealthGroupRemittance($validatedData = [])
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
    public function philhealthRemittanceSummary($validatedData = [])
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
    public function sssEmployeeLoans($validatedData = [])
    {
        $data = PayrollDetail::with(["employee.company_employments"])
        ->whereHas('payroll_record', function ($query) use ($validatedData) {
            return $query->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                ->isApproved();
        })
        ->with('loanPayments', function ($query) {
            return $query->where('name', EmployeeLoanType::SSS->value);
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
                "employee_name" => $employeeData->first()->employee->fullname_last,
                "employee_sss_id" => $employeeData->first()->employee->company_employments->sss_number,
                "total_payments" => $employeeData->first()->loanPayments()->sum("amount"),
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
    public function hdmfEmployeeLoans($validatedData = [])
    {
        $data = PayrollDetail::with(["employee.company_employments"])
        ->whereHas('payroll_record', function ($query) use ($validatedData) {
            return $query->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                ->isApproved();
        })
        ->with('loanPayments', function ($query) {
            return $query->where('name', EmployeeLoanType::HDMF_MPL->value);
        })
        ->hasPagibigContributions()
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
            'message' => 'Pagibig Employee Remittance Request fetched.',
            'data' => HdmfEmployeeLoansResource::collection($data),
        ];
    }
    public function coopEmployeeLoans($validatedData = [])
    {
        $data = PayrollDetail::with(["employee.company_employments"])
        ->whereHas('payroll_record', function ($query) use ($validatedData) {
            return $query->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                ->isApproved();
        })
        ->with('loanPayments', function ($query) {
            return $query->where('name', EmployeeLoanType::HDMF_MPL->value);
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
    public function coopGroupSummaryLoans($validatedData = [])
    {
        $data = PayrollDetail::with(["employee.company_employments"])
        ->whereHas('payroll_record', function ($query) use ($validatedData) {
            return $query->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                ->isApproved();
        })
        ->with('loanPayments', function ($query) {
            return $query->where('name', EmployeeLoanType::COOP->value);
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
    public function sssGroupSummaryLoans($validatedData = [])
    {
        $data = PayrollDetail::with(["employee.company_employments", "payroll_record"])
        ->whereHas('payroll_record', function ($query) use ($validatedData) {
            return $query
                ->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                ->isApproved();
        })
        ->with('loanPayments', function ($query) {
            return $query->where('name', EmployeeLoanType::SSS->value);
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
        ->map(function ($employeeData) {
            return [
                ...$employeeData->first()->toArray(),
                "employee_name" => $employeeData->first()->employee->fullname_last,
                "employee_sss_id" => $employeeData->first()->employee->company_employments->sss_number,
                "total_payments" => $employeeData->first()->loanPayments()->sum("amount"),
                "amount" => $employeeData->first()->loanPayments->last()->amount ?? 0,
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
    public function hdmfGroupSummaryLoans($validatedData = [])
    {
        $data = PayrollDetail::with(["employee.company_employments", "payroll_record"])
        ->whereHas('payroll_record', function ($query) use ($validatedData) {
            return $query->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                ->isApproved();
        })
        ->with('loanPayments', function ($query) {
            return $query->where('name', EmployeeLoanType::HDMF_MPL->value);
        })
        ->hasPagibigContributions()
        ->get()
        ->append([
            "total_pagibig_contribution",
        ])
        ->groupBy("employee_id")
        ->map(function ($employeeData) {
            return [
                ...$employeeData->first()->toArray(),
                "employee_name" => $employeeData->first()->employee->fullname_last,
                "employee_sss_id" => $employeeData->first()->employee->company_employments->sss_number,
                "total_payments" => $employeeData->first()->loanPayments()->sum("amount"),
                "amount" => $employeeData->first()->loanPayments->last()->amount ?? 0,
                "first_name" => $employeeData->first()->employee->first_name,
                "middle_name" => $employeeData->first()->employee->middle_name,
                "last_name" => $employeeData->first()->employee->family_name,
                "suffix_name" => $employeeData->first()->employee->suffix_name,
                "loan_type" => $employeeData->first()->loanPayments?->first()?->name,
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
}


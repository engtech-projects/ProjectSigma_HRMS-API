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
use App\Http\Resources\sssRemittanceSummaryResource;
use App\Models\PayrollDetail;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function sssEmployeeRemittanceGenerate(SssEmployeeRemittanceRequest $request)
    {
        $validatedData = $request->validated();
        $data = PayrollDetail::with(["employee.company_employments"])
        ->whereHas('payroll_record', function ($query) use ($validatedData) {
            return $query->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                ->isApproved();
        })
        ->where(function ($query) {
            $query->Where("sss_employee_contribution", ">", 0)
            ->orWhere("sss_employer_contribution", ">", 0)
            ->orWhere("sss_employee_compensation", ">", 0)
            ->orWhere("sss_employer_compensation", ">", 0)
            ->orWhere("sss_employee_wisp", ">", 0)
            ->orWhere("sss_employer_wisp", ">", 0);
        })
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
        return new JsonResponse([
            'success' => true,
            'message' => 'Employee Remittance Request fetched.',
            'data' => SSSEmployeeRemittanceResource::collection($data),
        ]);
    }
    public function sssGroupRemittanceGenerate(SssGroupRemittanceRequest $request)
    {
        $validatedData = $request->validated();
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
        ->where(function ($query) {
            $query->Where("sss_employee_contribution", ">", 0)
            ->orWhere("sss_employer_contribution", ">", 0)
            ->orWhere("sss_employee_compensation", ">", 0)
            ->orWhere("sss_employer_compensation", ">", 0)
            ->orWhere("sss_employee_wisp", ">", 0)
            ->orWhere("sss_employer_wisp", ">", 0);
        })
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
        return new JsonResponse([
            'success' => true,
            'message' => 'Project Remittance Request fetched.',
            'data' => [
                'charging' => $firstRecord['payroll_record']['charging_name'] ?? "",
                'remittances' => SssGroupRemittanceResource::collection($dataArray)
            ],
        ]);
    }
    public function sssRemittanceSummary(sssRemittanceSummaryRequest $request)
    {
        $validatedData = $request->validated();
        $data = PayrollDetail::with(['payroll_record', "employee.company_employments"])
        ->whereHas('payroll_record', function ($query) use ($validatedData) {
            return $query
                ->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                ->isApproved();
        })
        ->where(function ($query) {
            $query->Where("sss_employee_contribution", ">", 0)
            ->orWhere("sss_employer_contribution", ">", 0)
            ->orWhere("sss_employee_compensation", ">", 0)
            ->orWhere("sss_employer_compensation", ">", 0)
            ->orWhere("sss_employee_wisp", ">", 0)
            ->orWhere("sss_employer_wisp", ">", 0);
        })
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
        return new JsonResponse([
            'success' => true,
            'message' => 'Project Remittance Request fetched.',
            'data' => sssRemittanceSummaryResource::collection($uniqueGroup),
        ]);
    }
    public function pagibigEmployeeRemittanceGenerate(PagibigEmployeeRemittanceRequest $request)
    {
        $validatedData = $request->validated();
        $data = PayrollDetail::with(["employee.company_employments"])
        ->whereHas('payroll_record', function ($query) use ($validatedData) {
            return $query->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                ->isApproved();
        })
        ->where(function ($query) {
            $query->where("pagibig_employee_contribution", ">", 0)
            ->orWhere("pagibig_employer_contribution", ">", 0);
        })
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
        return new JsonResponse([
            'success' => true,
            'message' => 'Employee Remittance Request fetched.',
            'data' => PagibigEmployeeRemittanceResource::collection($data),
        ]);
    }
    public function pagibigGroupRemittanceGenerate(PagibigGroupRemittanceRequest $request)
    {
        $validatedData = $request->validated();
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
        ->where(function ($query) {
            $query->where("pagibig_employee_contribution", ">", 0)
            ->orWhere("pagibig_employer_contribution", ">", 0);
        })
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
        return new JsonResponse([
            'success' => true,
            'message' => 'Project Remittance Request fetched.',
            'data' => [
                'charging' => $firstRecord['payroll_record']['charging_name'] ?? "",
                'remittances' => PagibigGroupRemittanceResource::collection($dataArray)
            ],
        ]);
    }
    public function pagibigRemittanceSummary(PagibigRemittanceSummaryRequest $request)
    {
        $validatedData = $request->validated();
        $data = PayrollDetail::with(['payroll_record', "employee.company_employments"])
        ->whereHas('payroll_record', function ($query) use ($validatedData) {
            return $query
                ->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                ->isApproved();
        })
        ->where(function ($query) {
            $query->where("pagibig_employee_contribution", ">", 0)
            ->orWhere("pagibig_employer_contribution", ">", 0);
        })
        ->orderBy("created_at", "DESC")
        ->get()
        ->append([
            "total_pagibig_contribution",
        ])
        ->sortBy('payroll_record.charging_name', SORT_NATURAL)
        ->values()
        ->all();
        $uniqueGroup =  collect($data)->groupBy('payroll_record.charging_name');
        return new JsonResponse([
            'success' => true,
            'message' => 'Project Remittance Request fetched.',
            'data' => PagibigRemittanceSummaryResource::collection($uniqueGroup),
        ]);
    }
    public function philhealthEmployeeRemittanceGenerate(PhilhealthEmployeeRemittanceRequest $request)
    {
        $validatedData = $request->validated();
        $data = PayrollDetail::with(["employee.company_employments"])
        ->whereHas('payroll_record', function ($query) use ($validatedData) {
            return $query->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                ->isApproved();
        })
        ->where(function ($query) {
            $query->where("philhealth_employee_contribution", ">", 0)
            ->orWhere("philhealth_employer_contribution", ">", 0);
        })
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
        return new JsonResponse([
            'success' => true,
            'message' => 'Employee Remittance Request fetched.',
            'data' => PhilhealthEmployeeRemittanceResource::collection($data),
        ]);
    }
    public function philhealthGroupRemittanceGenerate(PhilhealthGroupRemittanceRequest $request)
    {
        $validatedData = $request->validated();
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
        ->where(function ($query) {
            $query->where("philhealth_employee_contribution", ">", 0)
            ->orWhere("philhealth_employer_contribution", ">", 0);
        })
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
        return new JsonResponse([
            'success' => true,
            'message' => 'Project Remittance Request fetched.',
            'data' => [
                'charging' => $firstRecord['payroll_record']['charging_name'] ?? "",
                'remittances' => PhilhealthGroupRemittanceResource::collection($dataArray)
            ],
        ]);
    }
    public function philhealthRemittanceSummary(PhilhealthRemittanceSummaryRequest $request)
    {
        $validatedData = $request->validated();
        $data = PayrollDetail::with(['payroll_record', "employee.company_employments"])
        ->whereHas('payroll_record', function ($query) use ($validatedData) {
            return $query
                ->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                ->isApproved();
        })
        ->where(function ($query) {
            $query->where("philhealth_employee_contribution", ">", 0)
            ->orWhere("philhealth_employer_contribution", ">", 0);
        })
        ->orderBy("created_at", "DESC")
        ->get()
        ->append([
            "total_philhealth_contribution"
        ])
        ->sortBy('payroll_record.charging_name', SORT_NATURAL)
        ->values()
        ->all();
        $uniqueGroup =  collect($data)->groupBy('payroll_record.charging_name');
        return new JsonResponse([
            'success' => true,
            'message' => 'Project Remittance Request fetched.',
            'data' => philhealthRemittanceSummaryResource::collection($uniqueGroup),
        ]);
    }
    public function sssEmployeeLoans(SssEmployeeLoansRequest $request)
    {
        $validatedData = $request->validated();
        $data = PayrollDetail::with(["employee.company_employments"])
        ->whereHas('payroll_record', function ($query) use ($validatedData) {
            return $query->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                ->isApproved();
        })
        ->with('loanPayments', function ($query) {
            return $query->where('name', EmployeeLoanType::SSS->value);
        })
        ->where(function ($query) {
            $query->Where("sss_employee_contribution", ">", 0)
            ->orWhere("sss_employer_contribution", ">", 0)
            ->orWhere("sss_employee_compensation", ">", 0)
            ->orWhere("sss_employer_compensation", ">", 0)
            ->orWhere("sss_employee_wisp", ">", 0)
            ->orWhere("sss_employer_wisp", ">", 0);
        })
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
        return new JsonResponse([
            'success' => true,
            'message' => 'Employee Remittance Request fetched.',
            'data' => SssEmployeeLoanResource::collection($data),
        ]);
    }
    public function hdmfEmployeeLoans(HdmfEmployeeLoansRequest $request)
    {
        $validatedData = $request->validated();
        $data = PayrollDetail::with(["employee.company_employments"])
        ->whereHas('payroll_record', function ($query) use ($validatedData) {
            return $query->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                ->isApproved();
        })
        ->with('loanPayments', function ($query) {
            return $query->where('name', EmployeeLoanType::HDMF_MPL->value);
        })
        ->where(function ($query) {
            $query->orWhere("pagibig_employer_contribution", ">", 0)
                ->orWhere("pagibig_employee_contribution", ">", 0);
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
        return new JsonResponse([
            'success' => true,
            'message' => 'Employee Remittance Request fetched.',
            'data' => HdmfEmployeeLoansResource::collection($data),
        ]);
    }
    public function sssGroupSummaryLoans(SssGroupSummaryLoansRequest $request)
    {
        $validatedData = $request->validated();
        $data = PayrollDetail::with(["employee.company_employments", "payroll_record"])
        ->whereHas('payroll_record', function ($query) use ($validatedData) {
            return $query
                ->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                ->isApproved();
        })
        ->with('loanPayments', function ($query) {
            return $query->where('name', EmployeeLoanType::SSS->value);
        })
        ->where(function ($query) {
            $query->where("sss_employee_contribution", ">", 0)
            ->orWhere("sss_employer_contribution", ">", 0)
            ->orWhere("sss_employee_compensation", ">", 0)
            ->orWhere("sss_employer_compensation", ">", 0)
            ->orWhere("sss_employee_wisp", ">", 0)
            ->orWhere("sss_employer_wisp", ">", 0);
        })
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
        return new JsonResponse([
            'success' => true,
            'message' => 'Employee Remittance Request fetched.',
            'data' => SssGroupSummaryLoansResource::collection($data),
        ]);
    }
    public function hdmfGroupSummaryLoans(HdmfGroupSummaryLoansRequest $request)
    {
        $validatedData = $request->validated();
        $data = PayrollDetail::with(["employee.company_employments", "payroll_record"])
        ->whereHas('payroll_record', function ($query) use ($validatedData) {
            return $query->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                ->isApproved();
        })
        ->with('loanPayments', function ($query) {
            return $query->where('name', EmployeeLoanType::HDMF_MPL->value);
        })
        ->where(function ($query) {
            $query->where("pagibig_employee_contribution", ">", 0)
            ->orWhere("pagibig_employer_contribution", ">", 0);
        })
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
        return new JsonResponse([
            'success' => true,
            'message' => 'Employee Remittance Request fetched.',
            'data' => HdmfGroupSummaryLoansResource::collection($data),
        ]);
    }
}

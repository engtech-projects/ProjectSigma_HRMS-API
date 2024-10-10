<?php

namespace App\Http\Controllers;

use App\Http\Requests\PagibigEmployeeRemittanceRequest;
use App\Http\Requests\PagibigGroupRemittanceRequest;
use App\Http\Requests\PagibigRemittanceSummaryRequest;
use App\Http\Requests\PhilhealthEmployeeRemittanceRequest;
use App\Http\Requests\PhilhealthGroupRemittanceRequest;
use App\Http\Requests\PhilhealthRemittanceSummaryRequest;
use App\Http\Requests\SssEmployeeRemittanceRequest;
use App\Http\Requests\SssGroupRemittanceRequest;
use App\Http\Requests\sssRemittanceSummaryRequest;
use App\Http\Resources\PagibigEmployeeRemittanceResource;
use App\Http\Resources\PagibigGroupRemittanceResource;
use App\Http\Resources\PagibigRemittanceSummaryResource;
use App\Http\Resources\PhilhealthEmployeeRemittanceResource;
use App\Http\Resources\PhilhealthGroupRemittanceResource;
use App\Http\Resources\philhealthRemittanceSummaryResource;
use App\Http\Resources\SSSEmployeeRemittanceResource;
use App\Http\Resources\SssGroupRemittanceResource;
use App\Http\Resources\sssRemittanceSummaryResource;
use App\Models\PayrollDetail;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    // SSS
    public function sssEmployeeRemittanceGenerate(SssEmployeeRemittanceRequest $request)
    {
        $validatedData = $request->validated();
        $data = PayrollDetail::with(["employee.company_employments"])
        ->whereHas('payroll_record', function($query) use ($validatedData) {
            return $query->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                ->isApproved();
        })
        ->where(function ($query) {
            $query->where("sss_employee_compensation", ">", 0)
            ->orWhere("sss_employee_contribution", ">", 0)
            ->orWhere("sss_employer_compensation", ">", 0)
            ->orWhere("sss_employer_contribution", ">", 0);
        })
        ->orderBy("created_at", "DESC")
        ->get()
        ->append([
            "total_sss_contribution",
            "total_sss_compensation",
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
                'total_sss_contribution' => $employeeData->sum("total_sss_contribution"),
                'total_sss_compensation' => $employeeData->sum("total_sss_compensation"),
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
        ->whereHas('payroll_record', function($query) use ($validatedData) {
            return $query->when(!empty($validatedData['project_id']), function($query2) use ($validatedData) {
                    return $query2->where('project_id', $validatedData["project_id"]);
                })
                ->when(!empty($validatedData['department_id']), function($query2) use ($validatedData) {
                    return $query2->where('department_id', $validatedData["department_id"]);
                })
                ->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                ->isApproved();
        })
        ->where(function ($query) {
            $query->where("sss_employee_compensation", ">", 0)
            ->orWhere("sss_employee_contribution", ">", 0)
            ->orWhere("sss_employer_compensation", ">", 0)
            ->orWhere("sss_employer_contribution", ">", 0);
        })
        ->orderBy("created_at", "DESC")
        ->get()
        ->append([
            "total_sss_contribution",
            "total_sss_compensation",
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
                'total_sss_contribution' => $employeeData->sum("total_sss_contribution"),
                'total_sss_compensation' => $employeeData->sum("total_sss_compensation"),
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
        ->whereHas('payroll_record', function($query) use ($validatedData) {
            return $query
                ->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                ->isApproved();
        })
        ->where(function ($query) {
            $query->where("sss_employee_compensation", ">", 0)
            ->orWhere("sss_employee_contribution", ">", 0)
            ->orWhere("sss_employer_compensation", ">", 0)
            ->orWhere("sss_employer_contribution", ">", 0);
        })
        ->orderBy("created_at", "DESC")
        ->get()
        ->append([
            "total_sss_contribution",
            "total_sss_compensation",
            "total_sss",
        ])
        ->sortBy('payroll_record.charging_name', SORT_NATURAL)
        ->groupBy("employee_id")
        ->map(function ($employeeData) {
            return [
                ...$employeeData->first()->toArray(),
                'sss_employer_contribution' => $employeeData->sum("sss_employer_contribution"),
                'sss_employee_contribution' => $employeeData->sum("sss_employee_contribution"),
                'sss_employer_compensation' => $employeeData->sum("sss_employer_compensation"),
                'sss_employee_compensation' => $employeeData->sum("sss_employee_compensation"),
                'total_sss_contribution' => $employeeData->sum("total_sss_contribution"),
                'total_sss_compensation' => $employeeData->sum("total_sss_compensation"),
                'total_sss' => $employeeData->sum("total_sss"),
                "payroll_record" => [
                    ...$employeeData->first()->payroll_record->toArray(),
                    "charging_name" => $employeeData->first()->payroll_record->charging_name,
                ]
            ];
        })
        ->values()
        ->all();
        $uniqueGroup =  collect($data)->groupBy('payroll_record.charging_name');
        return new JsonResponse([
            'success' => true,
            'message' => 'Project Remittance Request fetched.',
            'data' => sssRemittanceSummaryResource::collection($uniqueGroup),
        ]);
    }
    // PAGIBIG
    public function pagibigEmployeeRemittanceGenerate(PagibigEmployeeRemittanceRequest $request)
    {
        $validatedData = $request->validated();
        $data = PayrollDetail::with(["employee.company_employments"])
        ->whereHas('payroll_record', function($query) use ($validatedData) {
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
        ->whereHas('payroll_record', function($query) use ($validatedData) {
            return $query->when(!empty($validatedData['project_id']), function($query2) use ($validatedData) {
                    return $query2->where('project_id', $validatedData["project_id"]);
                })
                ->when(!empty($validatedData['department_id']), function($query2) use ($validatedData) {
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
            ];
        })
        ->values()
        ->all();
        $firstRecord = $data->first();
        $dataArray = $data->all();
        return new JsonResponse([
            'success' => true,
            'message' => 'Project Remittance Request fetched.',
            'data' => [
                'charging' => $firstRecord?->payroll_record->charging_name,
                'remittances' => PagibigGroupRemittanceResource::collection($dataArray)
            ],
        ]);
    }
    public function pagibigRemittanceSummary(PagibigRemittanceSummaryRequest $request)
    {
        $validatedData = $request->validated();
        $data = PayrollDetail::with(['payroll_record', "employee.company_employments"])
        ->whereHas('payroll_record', function($query) use ($validatedData) {
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
                ]
            ];
        })
        ->values()
        ->all();
        $uniqueGroup =  collect($data)->groupBy('payroll_record.charging_name');
        return new JsonResponse([
            'success' => true,
            'message' => 'Project Remittance Request fetched.',
            'data' => PagibigRemittanceSummaryResource::collection($uniqueGroup),
        ]);
    }
    // PHILHEALTH
    public function philhealthEmployeeRemittanceGenerate(PhilhealthEmployeeRemittanceRequest $request)
    {
        $validatedData = $request->validated();
        $data = PayrollDetail::with(["employee.company_employments"])
        ->whereHas('payroll_record', function($query) use ($validatedData) {
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
        ->whereHas('payroll_record', function($query) use ($validatedData) {
            return $query->when(!empty($validatedData['project_id']), function($query2) use ($validatedData) {
                    return $query2->where('project_id', $validatedData["project_id"]);
                })
                ->when(!empty($validatedData['department_id']), function($query2) use ($validatedData) {
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
        ->whereHas('payroll_record', function($query) use ($validatedData) {
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
                ]
            ];
        })
        ->values()
        ->all();
        $uniqueGroup =  collect($data)->groupBy('payroll_record.charging_name');
        return new JsonResponse([
            'success' => true,
            'message' => 'Project Remittance Request fetched.',
            'data' => philhealthRemittanceSummaryResource::collection($uniqueGroup),
        ]);
    }
}

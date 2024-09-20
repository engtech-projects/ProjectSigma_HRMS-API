<?php

namespace App\Http\Controllers;

use App\Http\Requests\PagibigEmployeeRemittanceRequest;
use App\Http\Requests\PagibigGroupRemittanceRequest;
use App\Http\Requests\PagibigRemittanceSummaryRequest;
use App\Http\Requests\PhilhealthEmployeeRemittanceRequest;
use App\Http\Requests\PhilhealthGroupRemittanceRequest;
use App\Http\Requests\SssEmployeeRemittanceRequest;
use App\Http\Requests\SssGroupRemittanceRequest;
use App\Http\Requests\SssRemittanceSummaryRequest;
use App\Http\Resources\PagibigEmployeeRemittanceResource;
use App\Http\Resources\PagibigGroupRemittanceResource;
use App\Http\Resources\PagibigRemittanceSummaryResource;
use App\Http\Resources\PhilhealthEmployeeRemittanceResource;
use App\Http\Resources\PhilhealthGroupRemittanceResource;
use App\Http\Resources\SSSEmployeeRemittanceResource;
use App\Http\Resources\SssGroupRemittanceResource;
use App\Http\Resources\sssRemittanceSummaryResource;
use App\Models\PayrollDetail;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function sssEmployeeRemittanceGenerate(SssEmployeeRemittanceRequest $request)
    {
        $validatedData = $request->validated();
        $data = PayrollDetail::whereHas('payroll_record', function($query) use ($validatedData) {
            return $query->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                ->isApproved();
        })
        ->orderBy("created_at", "DESC")
        ->get()
        ->sortBy('employee.fullname_first', SORT_NATURAL)
        ->values()
        ->all();

        return new JsonResponse([
            'success' => true,
            'message' => 'Employee Remittance Request fetched.',
            'data' => SSSEmployeeRemittanceResource::collection($data),
        ]);
    }
    public function pagibigEmployeeRemittanceGenerate(PagibigEmployeeRemittanceRequest $request)
    {
        $validatedData = $request->validated();
        $data = PayrollDetail::whereHas('payroll_record', function($query) use ($validatedData) {
            return $query->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                ->isApproved();
        })
        ->orderBy("created_at", "DESC")
        ->get()
        ->sortBy('employee.fullname_first', SORT_NATURAL)
        ->values()
        ->all();

        return new JsonResponse([
            'success' => true,
            'message' => 'Employee Remittance Request fetched.',
            'data' => PagibigEmployeeRemittanceResource::collection($data),
        ]);
    }
    public function philhealthEmployeeRemittanceGenerate(PhilhealthEmployeeRemittanceRequest $request)
    {
        $validatedData = $request->validated();
        $data = PayrollDetail::whereHas('payroll_record', function($query) use ($validatedData) {
            return $query->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                ->isApproved();
        })
        ->get()
        ->sortBy('employee.fullname_first', SORT_NATURAL)
        ->values()
        ->all();
        return new JsonResponse([
            'success' => true,
            'message' => 'Employee Remittance Request fetched.',
            'data' => PhilhealthEmployeeRemittanceResource::collection($data),
        ]);
    }
    public function sssGroupRemittanceGenerate(SssGroupRemittanceRequest $request)
    {
        $validatedData = $request->validated();
        $data = PayrollDetail::whereHas('payroll_record', function($query) use ($validatedData) {
            return $query->when(!empty($validatedData['project_id']), function($query2) use ($validatedData) {
                    return $query2->where('project_id', $validatedData["project_id"]);
                })
                ->when(!empty($validatedData['department_id']), function($query2) use ($validatedData) {
                    return $query2->where('department_id', $validatedData["department_id"]);
                })
                ->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                ->isApproved();
        })
        ->with(['payroll_record'])
        ->orderBy("created_at", "DESC")
        ->get()
        ->sortBy('employee.fullname_first', SORT_NATURAL)
        ->values();

        $firstRecord = $data->first();
        $dataArray = $data->all();

        return new JsonResponse([
            'success' => true,
            'message' => 'Project Remittance Request fetched.',
            'data' => [
                'charging' => $firstRecord?->payroll_record->charging_name,
                'remittances' => SssGroupRemittanceResource::collection($dataArray)
            ],
        ]);
    }
    public function pagibigGroupRemittanceGenerate(PagibigGroupRemittanceRequest $request)
    {
        $validatedData = $request->validated();
        $data = PayrollDetail::whereHas('payroll_record', function($query) use ($validatedData) {
            return $query->when(!empty($validatedData['project_id']), function($query2) use ($validatedData) {
                    return $query2->where('project_id', $validatedData["project_id"]);
                })
                ->when(!empty($validatedData['department_id']), function($query2) use ($validatedData) {
                    return $query2->where('department_id', $validatedData["department_id"]);
                })
                ->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                ->isApproved();
        })
        ->with(['payroll_record'])
        ->orderBy("created_at", "DESC")
        ->get()
        ->sortBy('employee.fullname_first', SORT_NATURAL)
        ->values();
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
    public function philhealthGroupRemittanceGenerate(PhilhealthGroupRemittanceRequest $request)
    {
        $validatedData = $request->validated();
        $data = PayrollDetail::whereHas('payroll_record', function($query) use ($validatedData) {
            return $query->when(!empty($validatedData['project_id']), function($query2) use ($validatedData) {
                    return $query2->where('project_id', $validatedData["project_id"]);
                })
                ->when(!empty($validatedData['department_id']), function($query2) use ($validatedData) {
                    return $query2->where('department_id', $validatedData["department_id"]);
                })
                ->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                ->isApproved();
        })
        ->with(['payroll_record'])
        ->orderBy("created_at", "DESC")
        ->get()
        ->sortBy('employee.fullname_first', SORT_NATURAL)
        ->values();
        $firstRecord = $data->first();
        $dataArray = $data->all();

        return new JsonResponse([
            'success' => true,
            'message' => 'Project Remittance Request fetched.',
            'data' => [
                'charging' => $firstRecord?->payroll_record->charging_name,
                'remittances' => PhilhealthGroupRemittanceResource::collection($dataArray)
            ],
        ]);
    }
    public function sssRemittanceSummary(SssRemittanceSummaryRequest $request)
    {
        $validatedData = $request->validated();
        $data = PayrollDetail::whereHas('payroll_record', function($query) use ($validatedData) {
            return $query
                ->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                ->isApproved();
        })
        ->with(['payroll_record'])
        ->orderBy("created_at", "DESC")
        ->get()
        ->append(['total_sss_contribution', 'total_sss_compensation', 'total_sss',])
        ->sortBy('employee.fullname_first', SORT_NATURAL)
        ->values();
        $uniqueGroup =  $data->groupBy('payroll_record.charging_name');
        return new JsonResponse([
            'success' => true,
            'message' => 'Project Remittance Request fetched.',
            'data' => sssRemittanceSummaryResource::collection($uniqueGroup),
        ]);
    }
    public function pagibigRemittanceSummary(PagibigRemittanceSummaryRequest $request)
    {
        $validatedData = $request->validated();
        $data = PayrollDetail::whereHas('payroll_record', function($query) use ($validatedData) {
            return $query
                ->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                ->isApproved();
        })
        ->with(['payroll_record'])
        ->orderBy("created_at", "DESC")
        ->get()
        ->append(['total_pagibig_contribution', 'total_pagibig_compensation', 'total_pagibig',])
        ->sortBy('employee.fullname_first', SORT_NATURAL)
        ->values();
        $uniqueGroup =  $data->groupBy('payroll_record.charging_name');
        return new JsonResponse([
            'success' => true,
            'message' => 'Project Remittance Request fetched.',
            'data' => PagibigRemittanceSummaryResource::collection($uniqueGroup),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\PagibigEmployeeRemittanceRequest;
use App\Http\Requests\PhilhealthEmployeeRemittanceRequest;
use App\Http\Requests\SssEmployeeRemittanceRequest;
use App\Http\Resources\PagibigEmployeeRemittanceResource;
use App\Http\Resources\PhilhealthEmployeeRemittanceResource;
use App\Http\Resources\SSSEmployeeRemittanceResource;
use App\Models\PayrollDetail;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function sssEmployeeRemittanceGenerate(SssEmployeeRemittanceRequest $request)
    {
        $validatedData = $request->validated();
        $data = PayrollDetail::whereHas('payroll_record', function($query) use ($validatedData) {
            return $query->whereYear('payroll_date', $validatedData['filter_year'])
                ->whereMonth('payroll_date', $validatedData['filter_month'])
                ->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                ->isApproved();
        })
        ->orderBy("created_at", "DESC")
        ->get();

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
            return $query->whereYear('payroll_date', $validatedData['filter_year'])
                ->whereMonth('payroll_date', $validatedData['filter_month'])
                ->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                ->isApproved();
        })
        ->orderBy("created_at", "DESC")
        ->get();

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
            return $query->whereYear('payroll_date', $validatedData['filter_year'])
                ->whereMonth('payroll_date', $validatedData['filter_month'])
                ->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                ->isApproved();
        })
        ->orderBy("created_at", "DESC")
        ->get();

        return new JsonResponse([
            'success' => true,
            'message' => 'Employee Remittance Request fetched.',
            'data' => PhilhealthEmployeeRemittanceResource::collection($data),
        ]);
    }
}

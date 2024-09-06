<?php

namespace App\Http\Controllers;

use App\Http\Requests\SssEmployeeRemittanceRequest;
use App\Http\Resources\SSSEmployeeRemittanceResource;
use App\Models\PayrollDetail;
use App\Utils\PaginateResourceCollection;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function sssEmployeeRemittanceGenerate(SssEmployeeRemittanceRequest $request)
    {
        $validatedData = $request->validated();
        $data = PayrollDetail::whereHas('payroll_record', function($query) use ($validatedData) {
            return $query->whereYear('payroll_date', $validatedData['filter_year'])
                ->whereMonth('payroll_date', $validatedData['filter_month'])
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
}

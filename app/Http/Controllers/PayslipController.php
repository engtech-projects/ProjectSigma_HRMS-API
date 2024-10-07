<?php

namespace App\Http\Controllers;
use App\Enums\DisbursementStatus;
use App\Http\Requests\PayslipRequest;
use App\Http\Resources\PayrollDetailsResource;
use App\Models\PayrollDetail;
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Http\Request;

class PayslipController extends Controller
{
    public function index(PayslipRequest $request)
    {
        $validatedData = $request->validated();
        $payroll_details = PayrollDetail::with(['employee.current_employment','payroll_record.salary_disbursement'])
            ->whereHas('payroll_record', function ($query) {
                return $query->whereHas('salary_disbursement', function ($query2) {
                    return $query2->where('disbursement_status', DisbursementStatus::RELEASED->value);
                })->isApproved();
            })
            ->whereIn('id', $validatedData['ids'])
            ->orderBy('created_at', 'DESC')
            ->get();
        return view('reports.docs.payslip', [ 'payroll_details' => PayrollDetailsResource::collection($payroll_details)]);
    }
}

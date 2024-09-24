<?php

namespace App\Http\Resources;

use App\Models\PayrollDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestPayrollSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $payrollRecordIds = $this->payroll_records->pluck("id");
        $payrollDetails = PayrollDetail::whereIn("payroll_record_id", $payrollRecordIds)
        ->with(['payroll_record'])
        ->orderBy("created_at", "DESC")
        ->get()
        ->append([
            'total_basic_pays',
            'total_overtime_pays',
            'total_cash_advance_payments',
            'total_loan_payments',
            'total_other_deduction_payments',
        ])
        ->sortBy('employee.fullname_first', SORT_NATURAL)
        ->values();
        $uniqueGroup =  $payrollDetails->groupBy('payroll_record.charging_name');
        return [
            ...parent::toArray($request),
            "payroll_date_human" => $this->payroll_date_human,
            "created_by_user_name" => $this->created_by_user_name,
            "summary" => PayrollRecordsPayrollSummaryResource::collection($uniqueGroup),
            "approvals" => ApprovalAttributeResource::collection($this->approvals),
            "next_approval" => $this->getNextPendingApproval(),
        ];
    }
}

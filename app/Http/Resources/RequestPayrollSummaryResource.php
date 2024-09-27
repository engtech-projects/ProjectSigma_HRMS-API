<?php

namespace App\Http\Resources;

use App\Http\Services\Payroll\SalaryDisbursementService;
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
        $payrollSummaryDatas = SalaryDisbursementService::getPayrollSummary($payrollRecordIds);
        return [
            ...parent::toArray($request),
            "payroll_date_human" => $this->payroll_date_human,
            "created_by_user_name" => $this->created_by_user_name,
            "summary" => PayrollRecordsPayrollSummaryResource::collection($payrollSummaryDatas),
            "approvals" => ApprovalAttributeResource::collection($this->approvals),
            "next_approval" => $this->getNextPendingApproval(),
        ];
    }
}

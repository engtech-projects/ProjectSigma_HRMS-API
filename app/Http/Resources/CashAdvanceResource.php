<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CashAdvanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $this->when('user', function () {
            return [
                    "fullname_first" => $this->employee?->fullname_first,
            ];
        });
        return [
            "id" => $this->id,
            "employee" => $user,
            "employee_id" => $this->employee_id,
            "department_id" => $this->department_id,
            "project_id" => $this->project_id,
            "terms_of_payment" => $this->terms_of_payment,
            "amount" => $this->amount,
            "amount_requested" => $this->amount_requested,
            "amount_approved" => $this->amount_approved,
            "installment_deduction" => $this->installment_deduction,
            "deduction_date_start" => $this->deduction_date_start->format('F j, Y'),
            "purpose" => $this->purpose,
            "remaining_balance" => $this->balance,
            "total_paid" => $this->total_paid,
            "terms_of_cash_advance" => $this->terms_of_cash_advance,
            "remarks" => $this->remarks,
            "request_status" => $this->request_status,
            "department" => $this->department,
            "project" => $this->project,
            "approvals" => ApprovalAttributeResource::collection($this->approvals),
            "next_approval" => $this->getNextPendingApproval(),
            "created_by" => $this->created_by,
            "payments" => CashAdvancePaymentResource::collection($this->cashAdvancePayments),
            "amount_formatted" => number_format($this->amount, 2),
            "amount_requested_formatted" => number_format($this->amount_requested, 2),
            "amount_approved_formatted" => number_format($this->amount_approved, 2),
            "installment_deduction_formatted" => number_format($this->installment_deduction, 2),
            "remaining_balance_formatted" => number_format($this->balance, 2),
            "total_paid_formatted" => number_format($this->total_paid, 2),

        ];
    }
}

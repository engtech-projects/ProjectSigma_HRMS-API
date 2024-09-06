<?php

namespace App\Traits;

use Illuminate\Support\Carbon;
use App\Enums\RequestApprovalStatus;
use App\Enums\RequestStatusType;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

trait HasApproval
{
    public function completeRequestStatus()
    {
        $this->request_status = RequestApprovalStatus::APPROVED;
        $this->save();
        $this->refresh();
    }
    public function denyRequestStatus()
    {
        $this->request_status = RequestApprovalStatus::DENIED;
        $this->save();
        $this->refresh();
    }
    public function setRequestStatus(?string $newStatus)
    {
    }
    public function requestStatusCompleted(): bool
    {
        return false;
    }
    public function requestStatusEnded(): bool
    {
        return false;
    }
    public function scopeAuthUserPending(Builder $query): void
    {
        $query->whereJsonLength('approvals', '>', 0)
            ->whereJsonContains('approvals', ['user_id' => auth()->user()->id, 'status' => RequestApprovalStatus::PENDING]);
    }
    public function scopeIsApproved(Builder $query): void
    {
        $query->where('request_status', RequestStatusType::APPROVED->value);
    }

    public function getUserPendingApproval($userId)
    {
        return collect($this->approvals)->where('user_id', $userId)
            ->where('status', RequestApprovalStatus::PENDING);
    }
    public function getNextPendingApproval()
    {
        if($this->request_status != RequestStatusType::PENDING->value) {
            return null;
        }
        return collect($this->approvals)->where('status', RequestApprovalStatus::PENDING)->first();
    }

    public function approveCurrentApproval()
    {
        // USE THIS FUNCTION IF SURE TO APPROVE CURRENT APPROVAL AND VERIFIED IF CURRENT APPROVAL IS CURRENT USER
        $currentApproval = $this->getNextPendingApproval();
        $currentApprovalIndex = collect($this->approvals)->search($currentApproval);
        $this->approvals = collect($this->approvals)->map(function ($approval, $index) use($currentApprovalIndex) {
            if ($index === $currentApprovalIndex) {
                $approval["status"] = RequestApprovalStatus::APPROVED;
                $approval["date_approved"] = Carbon::now();
            }
            return $approval;
        });
        $this->save();
        $this->refresh();
        if (collect($this->approvals)->last()['status'] === RequestApprovalStatus::APPROVED) {
            $this->completeRequestStatus();
        }
    }

    public function denyCurrentApproval($remarks)
    {
        // USE THIS FUNCTION IF SURE TO DENY CURRENT APPROVAL AND VERIFIED IF CURRENT APPROVAL IS CURRENT USER
        $currentApproval = $this->getNextPendingApproval();
        $currentApprovalIndex = collect($this->approvals)->search($currentApproval);
        $this->approvals = collect($this->approvals)->map(function ($approval, $index) use($currentApprovalIndex, $remarks) {
            if ($index === $currentApprovalIndex) {
                $approval["status"] = RequestApprovalStatus::DENIED;
                $approval["date_denied"] = Carbon::now();
                $approval["remarks"] = $remarks;
            }
            return $approval;
        });
        $this->save();
        $this->denyRequestStatus();
    }

    public function updateApproval(?array $data)
    {
        // CHECK IF MANPOWER REQUEST ALREADY DISAPPROVED AND SET RESPONSE DATA
        if ($this->requestStatusEnded()) {
            return [
                "approvals" => $this->approvals,
                'success' => false,
                "status_code" => JsonResponse::HTTP_FORBIDDEN,
                "message" => "The request was already ended.",
            ];
        }
        // CHECK IF MANPOWER REQUEST ALREADY COMPLETED AND SET RESPONSE DATA
        if ($this->requestStatusCompleted()) {
            return [
                "approvals" => $this->approvals,
                'success' => false,
                "status_code" => JsonResponse::HTTP_FORBIDDEN,
                "message" => "The request was already completed.",
            ];
        }
        $currentApproval = $this->getNextPendingApproval();
        // CHECK IF THERE IS A CURRENT APPROVAL AND IF IS FOR THE LOGGED IN USER
        if (!empty($currentApproval) && $currentApproval['user_id'] != auth()->user()->id) {
            return [
                "approvals" => $this->approvals,
                'success' => false,
                "status_code" => JsonResponse::HTTP_FORBIDDEN,
                "message" => "Failed to {$data['status']}. Your approval is for later or already done.",
            ];
        }
        DB::beginTransaction();
        // UPDATE CURRENT APPROVAL TO DENIED/APPROVED
        if ($data['status'] === RequestApprovalStatus::DENIED) {
            $this->denyCurrentApproval($data["remarks"]);
        } else {
            $this->approveCurrentApproval();
        }
        DB::commit();
        return [
            "approvals" => $currentApproval,
            'success' => true,
            "status_code" => JsonResponse::HTTP_OK,
            "message" => $data['status'] === RequestApprovalStatus::APPROVED ? "Successfully approved." : "Successfully denied.",
        ];
    }
}

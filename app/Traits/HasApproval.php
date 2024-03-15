<?php

namespace App\Traits;

use Illuminate\Support\Carbon;
use App\Models\ManpowerRequest;
use Illuminate\Support\Collection;
use App\Enums\ManpowerRequestStatus;
use App\Enums\ManpowerApprovalStatus;
use Illuminate\Http\JsonResponse;

trait HasApproval
{
    public function getUserPendingApproval($approvals, $userId)
    {
        return $approvals->where('user_id', $userId)
            ->where('status', ManpowerApprovalStatus::PENDING);
    }
    public function getNextPendingApproval($approvals)
    {
        return $approvals->where('status', ManpowerApprovalStatus::PENDING)->first();
    }

    public function setApproval(ManpowerRequest $manpowerRequest)
    {

    }

    public function updateApproval($manpowerRequestApproval, ManpowerRequest $manpowerRequest, ?array $data)
    {
        $userApproval = $this->getUserPendingApproval($manpowerRequestApproval, auth()->user()->id)->first();
        $nextApproval = $this->getNextPendingApproval(collect($manpowerRequest->approvals));

        if ($manpowerRequest->request_status === ManpowerRequestStatus::DISAPPROVED) {
            return [
                "approvals" => $manpowerRequestApproval,
                'success' => false,
                "status_code" => JsonResponse::HTTP_FORBIDDEN,
                "message" => "Manpower request was already disapproved",
            ];
        }
        if ($nextApproval['user_id'] !== auth()->user()->id) {
            return [
                "approvals" => $manpowerRequestApproval,
                'success' => false,
                "status_code" => JsonResponse::HTTP_FORBIDDEN,
                "message" => "Failed to approve. Your approval is for later or already done.",
            ];
        }

        $approvalToUpdate = $manpowerRequestApproval->search($userApproval);
        $manpowerRequestApproval = collect($manpowerRequest->approvals)->map(function ($item, int $key) use ($approvalToUpdate, $data) {
            if ($key === $approvalToUpdate) {
                $item['status'] = $data['status'];
                $item['date_approved'] = $data['status'] === ManpowerApprovalStatus::APPROVED ? Carbon::now()->format('Y-m-d') : $item["date_approved"];
                $item['remarks'] = array_key_exists("remarks", $data) ? $data["remarks"] : $item["remarks"];
            }
            return $item;
        });
        $isRequestApproved =  $manpowerRequestApproval->last()['status'] == ManpowerRequestStatus::APPROVED ? true : false;
        if ($isRequestApproved) {
            $manpowerRequest->request_status = ManpowerRequestStatus::APPROVED;
        } else {
            $isApprovalDenied = $manpowerRequestApproval->contains(function ($approval) {
                return $approval['status'] === ManpowerApprovalStatus::DENIED;
            });

            if ($isApprovalDenied) {
                $manpowerRequest->request_status = ManpowerRequestStatus::DISAPPROVED;
            }
        }
        $manpowerRequest->approvals = $manpowerRequestApproval;
        $manpowerRequest->save();

        return [
            "approvals" => $manpowerRequestApproval,
            'success' => true,
            "status_code" => JsonResponse::HTTP_OK,
            "message" => $data['status'] === ManpowerApprovalStatus::APPROVED ? "Manpower request successfully approved." : "Manpower request successfully denied.",
        ];
    }

    /* public function updateApproval($manpowerRequestApproval, ManpowerRequest $manpowerRequest, ?array $data)
    {
        $userApproval = $this->getUserPendingApproval($manpowerRequestApproval, auth()->user()->id)->first();
        if ($manpowerRequest->request_status === ManpowerRequestStatus::DISAPPROVED) {
            return [
                "approvals" => $manpowerRequestApproval,
                "stratus" => ManpowerRequestStatus::DISAPPROVED,
                'success' => true,
                "message" => "Manpower request already been disapproved",
            ];
        }

        $approvalToUpdate = $manpowerRequestApproval->search($userApproval);
        $manpowerRequestApproval = collect($manpowerRequest->approvals)->map(function ($item, int $key) use ($approvalToUpdate, $data) {
            if ($key === $approvalToUpdate) {
                $item['status'] = $data['status'];
                $item['date_approved'] = $data['status'] === ManpowerApprovalStatus::APPROVED ? Carbon::now()->format('Y-m-d') : $item["date_approved"];
                $item['remarks'] = array_key_exists("remarks", $data) ? $data["remarks"] : $item["remarks"];
            }
            return $item;
        });
        $manpowerRequest->request_status = $data['status'];
        $manpowerRequest->approvals = $manpowerRequestApproval;
        $manpowerRequest->save();

        return [
            "approvals" => $manpowerRequestApproval,
            "stratus" => $data['status'],
            'success' => true,
            "message" => $data['status'] === ManpowerApprovalStatus::APPROVED ? "Manpower request successfully approved." : "Manpower request successfully denied.",
        ];
    } */
}

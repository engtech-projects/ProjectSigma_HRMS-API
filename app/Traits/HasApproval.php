<?php

namespace App\Traits;

use Illuminate\Support\Carbon;
use App\Models\ManpowerRequest;
use App\Enums\ManpowerRequestStatus;
use Illuminate\Support\Collection;

trait HasApproval
{
    public function getUserPendingApproval($approvals, $userId)
    {
        return $approvals->where('user_id', $userId)
            ->where('status', ManpowerRequestStatus::PENDING);
    }
    public function getNextPendingApproval($approvals, $userId)
    {
        return $approvals->where('status', ManpowerRequestStatus::PENDING)->first();
    }

    public function updateApproval($manpowerRequestApproval, ManpowerRequest $manpowerRequest, $data)
    {
        $userApproval = $this->getUserPendingApproval($manpowerRequestApproval, auth()->user()->id)->first();
        if ($userApproval) {
            $approvalToUpdate = $manpowerRequestApproval->search($userApproval);
            $manpowerRequestApproval = collect($manpowerRequest->approvals)->map(function ($item, int $key) use ($approvalToUpdate, $data) {
                if ($key === $approvalToUpdate) {
                    $item['status'] = $data['status'];
                    $item['date_approved'] = $data['status'] === ManpowerRequestStatus::APPROVED ? Carbon::now()->format('Y-m-d') : $item["date_approved"];
                    $item['remarks'] = array_key_exists("remarks", $data) ? $data["remarks"] : $item["remarks"];
                }
                return $item;
            });
            return [
                "approvals" => $manpowerRequestApproval,
                'success' => true,
                "message" => $data['status'] === ManpowerRequestStatus::APPROVED ? "Successfully approved the manpower request" : "Failed to approve. Your approval is for later or already done.",
            ];
        }
        return [];
    }
}

<?php

namespace App\Traits;

use Illuminate\Support\Carbon;
use App\Models\ManpowerRequest;
use Illuminate\Support\Collection;
use App\Enums\ManpowerRequestStatus;
use App\Enums\ManpowerApprovalStatus;
use App\Models\User;
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

    public function getApprovalsAttribute($value)
    {
        $value = json_decode($value, true);
        foreach ($value as &$approval) {
            $user = User::with('employee')->find($approval['user_id']);
            if ($user) {
                $approval['employee'] = [
                    'employee_id' => $user->employee_id,
                    'full_name' =>  $user->employee->fullnameLast,
                    'type' => $user->type
                ];
            }
        }
        return $value;
    }

    public function getAllManpowerRequest()
    {
        $userId = auth()->user()->id;
        return $this->manpowerRequest->requestStatusPending()
            ->with(['user'])
            ->whereJsonLength('approvals', '>', 0)
            ->whereJsonContains('approvals', ['user_id' => $userId, 'status' => ManpowerApprovalStatus::PENDING])
            ->get();
    }

    public function setNewApproval(ManpowerRequest $manpowerRequest, $approvalToUpdate, $data)
    {
        $manpowerRequestApproval = collect($manpowerRequest->approvals)->map(function ($item, int $key) use ($approvalToUpdate, $data) {
            if ($key === $approvalToUpdate) {
                $item['status'] = $data['status'];
                if ($data["status"] === ManpowerApprovalStatus::DENIED) {
                    $data['date_approved'] = Carbon::now()->format('Y-m-d');
                } else {
                    $data['date_denied'] = Carbon::now()->format('Y-m-d');
                }
                $item['remarks'] = array_key_exists("remarks", $data) ? $data["remarks"] : $item["remarks"];
            }
            return $item;
        });
        return $manpowerRequestApproval;
    }

    public function setNewManpowerRequestStatus(ManpowerRequest $manpowerRequest, ?object $manpowerRequestApproval, ?bool $isRequestApproved)
    {;
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
    }

    public function updateApproval($manpowerRequestApproval, ManpowerRequest $manpowerRequest, ?array $data)
    {
        $userApproval = $this->getUserPendingApproval($manpowerRequestApproval, auth()->user()->id)->first();
        $nextApproval = $this->getNextPendingApproval(collect($manpowerRequest->approvals));

        // CHECK IF MANPOWER REQUEST ALREADY APPROVED AND SET RESPONSE DATA
        if ($manpowerRequest->request_status === ManpowerRequestStatus::DISAPPROVED) {
            return [
                "approvals" => $manpowerRequestApproval,
                'success' => false,
                "status_code" => JsonResponse::HTTP_FORBIDDEN,
                "message" => "Manpower request was already disapproved",
            ];
        }
        // CHECK IF THE CURRENT USER HAS PENDING APPROVAL AND SET RESPONSE DATA
        if ($nextApproval && $nextApproval['user_id'] !== auth()->user()->id) {
            return [
                "approvals" => $manpowerRequestApproval,
                'success' => false,
                "status_code" => JsonResponse::HTTP_FORBIDDEN,
                "message" => "Failed to approve. Your approval is for later or already done.",
            ];
        }
        // SET NEW MAN POWER REQUEST APPROVAL FOR RESOURCE UPDATE
        $approvalToUpdate = $manpowerRequestApproval->search($userApproval);
        $manpowerRequestApproval = $this->setNewApproval($manpowerRequest, $approvalToUpdate, $data);
        // SET NEW MANPOWER REQUEST STATUS FOR RESOURCE UPDATE
        $isRequestApproved =  $manpowerRequestApproval->last()['status'] == ManpowerRequestStatus::APPROVED ? true : false;
        $this->setNewManpowerRequestStatus($manpowerRequest, $manpowerRequestApproval, $isRequestApproved);
        // SAVE NEW RESOURCE FOR MANPOWER REQUEST
        $manpowerRequest->approvals = $manpowerRequestApproval;
        $manpowerRequest->save();

        return [
            "approvals" => $manpowerRequestApproval,
            'success' => true,
            "status_code" => JsonResponse::HTTP_OK,
            "message" => $data['status'] === ManpowerApprovalStatus::APPROVED ? "Manpower request successfully approved." : "Manpower request successfully denied.",
        ];
    }
}

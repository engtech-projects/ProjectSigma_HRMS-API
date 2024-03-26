<?php

namespace App\Traits;

use Illuminate\Support\Carbon;
use App\Models\ManpowerRequest;
use Illuminate\Support\Collection;
use App\Enums\ManpowerRequestStatus;
use App\Enums\RequestApprovalStatus;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use PhpOffice\PhpSpreadsheet\Calculation\Logical\Boolean;

trait HasApproval
{
    public function getUserPendingApproval($userId)
    {
        return collect($this->approvals)->where('user_id', $userId)
            ->where('status', RequestApprovalStatus::PENDING);
    }
    public function getNextPendingApproval()
    {
        return collect($this->approvals)->where('status', RequestApprovalStatus::PENDING)->first();
    }


    public function setNewApproval($approvalToUpdate, $data)
    {
        $manpowerRequestApproval = collect($this->approvals)->map(function ($item, int $key) use ($approvalToUpdate, $data) {
            if ($key === $approvalToUpdate) {

                $item['status'] = $data['status'];
                if ($data["status"] === RequestApprovalStatus::DENIED) {
                    $data['date_denied'] = Carbon::now()->format('Y-m-d');
                } else {
                    $data['date_approved'] = Carbon::now()->format('Y-m-d');
                }
                $item['remarks'] = array_key_exists("remarks", $data) ? $data["remarks"] : $item["remarks"];
            }
            return $item;
        });
        return $manpowerRequestApproval;
    }

    public function completeRequestStatus()
    {

    }
    public function denyRequestStatus()
    {

    }

    public function setRequestStatus(?string $newStatus)
    {

    }


    public function requestStatusCompleted() : bool
    {
        return false;
    }

    public function requestStatusDisapproved() : bool
    {
        return false;
    }

    public function requestStatusCancelled() : bool
    {
        return false;
    }

    public function updateApproval(?array $data)
    {
        $userApproval = $this->getUserPendingApproval(auth()->user()->id)->first();
        $nextApproval = $this->getNextPendingApproval();

        // CHECK IF MANPOWER REQUEST ALREADY DISAPPROVED AND SET RESPONSE DATA
        if ($this->requestStatusDisapproved()) {
            return [
                "approvals" => $this->approvals,
                'success' => false,
                "status_code" => JsonResponse::HTTP_FORBIDDEN,
                "message" => "The request was already disapproved.",
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
        // CHECK IF MANPOWER REQUEST ALREADY CANCELLED AND SET RESPONSE DATA
        if ($this->requestStatusCancelled()) {
            return [
                "approvals" => $this->approvals,
                'success' => false,
                "status_code" => JsonResponse::HTTP_FORBIDDEN,
                "message" => "The request was already cancelled.",
            ];
        }

        // CHECK IF THE CURRENT USER HAS PENDING APPROVAL AND SET RESPONSE DATA
        if (!empty($nextApproval) && $nextApproval['user_id'] != auth()->user()->id) {
            return [
                "approvals" => $this->approvals,
                'success' => false,
                "status_code" => JsonResponse::HTTP_FORBIDDEN,
                "message" => "Failed to {$data['status']}. Your approval is for later or already done.",
            ];
        }
        // SET NEW MAN POWER REQUEST APPROVAL FOR RESOURCE UPDATE
        $approvalToUpdate = $this->approvals->search($userApproval);
        $newApproval = $this->setNewApproval($approvalToUpdate, $data);
        // SAVE NEW RESOURCE FOR MANPOWER REQUEST
        $this->approvals = $newApproval;
        $this->save();
        if(RequestApprovalStatus::DENIED === $data['status']){
            $this->denyRequestStatus();
        }
        dd($newApproval);
        // IF LAST APPROVAL
        /* if () {
            $this->completeRequestStatus();
        } */


        return [
            "approvals" => $newApproval,
            'success' => true,
            "status_code" => JsonResponse::HTTP_OK,
            "message" => $data['status'] === RequestApprovalStatus::APPROVED ? "Successfully approved." : "Successfully denied.",
        ];
    }
}

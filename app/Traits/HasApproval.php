<?php

namespace App\Traits;

use Illuminate\Support\Carbon;
use App\Models\ManpowerRequest;
use Illuminate\Support\Collection;
use App\Enums\ManpowerRequestStatus;
use App\Enums\RequestApprovalStatus;
use App\Models\User;
use Illuminate\Http\JsonResponse;

trait HasApproval
{
    public function getUserPendingApproval($approvals, $userId)
    {
        return $approvals->where('user_id', $userId)
            ->where('status', RequestApprovalStatus::PENDING);
    }
    public function getNextPendingApproval($approvals)
    {
        return $approvals->where('status', RequestApprovalStatus::PENDING)->first();
    }

    public function getApprovalsAttribute($value)
    {
        $value = json_decode($value, true);
        foreach ($value as &$approval) {
            $user = User::with('employee')->find($approval['user_id']);
            if ($user && $user->employee) {
                $approval['employee'] = [
                    'id' => $user->employee_id,
                    'fullname_last' =>  $user->employee->fullname_last,
                    'fullname_first' => $user->employee->fullname_first,
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
            ->with(['user.employee'])
            ->whereJsonLength('approvals', '>', 0)
            ->whereJsonContains('approvals', ['user_id' => $userId, 'status' => RequestApprovalStatus::PENDING])
            ->get();
    }

    public function setNewApproval($model, $approvalToUpdate, $data)
    {
        $manpowerRequestApproval = collect($model->approvals)->map(function ($item, int $key) use ($approvalToUpdate, $data) {
            if ($key === $approvalToUpdate) {

                $item['status'] = $data['status'];
                if ($data["status"] === RequestApprovalStatus::DENIED) {
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

    public function setNewManpowerRequestStatus($model, ?object $manpowerRequestApproval, ?bool $isRequestApproved)
    {
        if ($isRequestApproved) {
            $model->request_status = ManpowerRequestStatus::APPROVED;
        } else {
            $isApprovalDenied = $manpowerRequestApproval->contains(function ($approval) {
                return $approval['status'] === RequestApprovalStatus::DENIED;
            });

            if ($isApprovalDenied) {
                $model->request_status = ManpowerRequestStatus::DISAPPROVED;
            }
        }
    }

    public function setActionResponse()
    {
    }

    public function updateApproval($model, ?array $data)
    {
        $approvals = collect($model->approvals);
        $userApproval = $this->getUserPendingApproval($approvals, auth()->user()->id)->first();
        $nextApproval = $this->getNextPendingApproval(collect($model->approvals));

        // CHECK IF MANPOWER REQUEST ALREADY APPROVED AND SET RESPONSE DATA
        if ($model->request_status === ManpowerRequestStatus::DISAPPROVED) {
            return [
                "approvals" => $approvals,
                'success' => false,
                "status_code" => JsonResponse::HTTP_FORBIDDEN,
                "message" => "The request was already disapproved",
            ];
        }
        if ($model->request_status === ManpowerRequestStatus::APPROVED) {
            return [
                "approvals" => $approvals,
                'success' => false,
                "status_code" => JsonResponse::HTTP_FORBIDDEN,
                "message" => "The request was already approved",
            ];
        }
        // CHECK IF THE CURRENT USER HAS PENDING APPROVAL AND SET RESPONSE DATA
        if (!empty($nextApproval) && $nextApproval['user_id'] != auth()->user()->id) {
            return [
                "approvals" => $approvals,
                'success' => false,
                "status_code" => JsonResponse::HTTP_FORBIDDEN,
                "message" => "Failed to approve. Your approval is for later or already done.",
            ];
        }
        // SET NEW MAN POWER REQUEST APPROVAL FOR RESOURCE UPDATE
        $approvalToUpdate = $approvals->search($userApproval);
        $newApproval = $this->setNewApproval($model, $approvalToUpdate, $data);
        // SET NEW MANPOWER REQUEST STATUS FOR RESOURCE UPDATE
        $isRequestApproved =  $newApproval->last()['status'] == ManpowerRequestStatus::APPROVED ? true : false;
        //$this->setNewManpowerRequestStatus($model, $newApproval, $isRequestApproved);
        // SAVE NEW RESOURCE FOR MANPOWER REQUEST
        $model->approvals = $newApproval;
        $model->save();

        return [
            "approvals" => $newApproval,
            'success' => true,
            "status_code" => JsonResponse::HTTP_OK,
            "message" => $data['status'] === RequestApprovalStatus::APPROVED ? "Successfully approved." : "Successfully denied.",
        ];
    }
}

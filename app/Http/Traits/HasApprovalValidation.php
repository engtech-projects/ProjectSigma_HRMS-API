<?php

namespace App\Http\Traits;

trait HasApprovalValidation
{
    public function prepareApprovalValidation()
    {
        if (gettype($this->approvals) == "string") {
            $this->merge([
                "approvals" => json_decode($this->approvals, true)
            ]);
        }
    }

    public function storeApprovals()
    {
        $data = [
            'approvals' => [
                "required",
                "array",
            ],
            'approvals.*' => [
                "required",
                "array",
                function ($attribute, $value, $fail) {
                    if ($value['selector_type'] != "specific" && auth()->user()->id == $value["user_id"]) {
                        $fail("Can't set yourself as an approver.");
                    }
                },
            ],
            'approvals.*.type' => [
                "required",
                "string",
            ],
            'approvals.*.user_id' => [
                "required",
                "integer",
                "exists:users,id",
            ],
            'approvals.*.selector_type' => [ // ADDED TO REQUIREMENTS TO IDENTIFY IF THE SELECTOR TYPE IS SPECIFIC FOR A USER
                "required",
                "string",
            ],
            'approvals.*.status' => [ // SHOULD BE NOT REQUIRED AND DEFAULTED TO PENDING
                "required",
                "string",
            ],
            'approvals.*.date_approved' => [
                "nullable",
                "date",
            ],
            'approvals.*.remarks' => [
                "nullable",
                "string",
            ]
        ];
        return $data;
    }

    public function updateApprovals()
    {
        $data = [
            'approvals' => [
                "nullable",
                "array",
            ],
            'approvals.*' => [
                "nullable",
                "array",
            ],
            'approvals.*.type' => [
                "nullable",
                "string",
            ],
            'approvals.*.user_id' => [
                "nullable",
                "integer",
                "exists:users,id",
            ],
            'approvals.*.status' => [
                "nullable",
                "string",
            ],
            'approvals.*.date_approved' => [
                "nullable",
                "date",
            ],
            'approvals.*.remarks' => [
                "nullable",
                "string",
            ]
        ];
        return $data;
    }
}

<?php

namespace App\Http\Traits;

trait ApprovalsRequest
{
    function storeApprovals()
    {
        return $data = [
            'approvals.*' => [
                "required",
                "array",
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
            'approvals.*.status' => [
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
    }

    function updateApprovals()
    {
        return $data = [
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
    }
}

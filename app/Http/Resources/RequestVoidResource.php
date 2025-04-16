<?php

namespace App\Http\Resources;

use App\Enums\VoidRequestModels;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestVoidResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            ...parent::toArray($request),
            "approvals" => ApprovalAttributeResource::collection($this->approvals),
            "next_approval" => $this->getNextPendingApproval(),
            "created_at_human" => $this->created_at_human,
            "created_by_user_name" => $this->created_by_user_name,
            "void_type" => $this->void_type_name,
            "request" => $this->whenLoaded("request", function ($request) {
                if ($this->request_type == VoidRequestModels::RequestLeaves->value) {
                    return new EmployeeLeaveResource($request);
                }
            })
        ];
    }
}

<?php

namespace App\Http\Resources;

use App\Enums\ApprovalStatus;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HrmsServiceApprovalAttributeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = User::with('employee')->find($this["user_id"]);
        return [
            "type" => $this["type"],
            "status" => $this["status"] ?? null,
            "selector_type" => $this["selector_type"] ?? null,
            "user_id" => $this["user_id"] ?? null,
            "remarks" => $this["remarks"] ?? null,
            "date_approved" => $this["date_approved"] ?? null,
            "date_approved_human" => ($this["date_approved"] ?? null) ? Carbon::parse($this["date_approved"])->format('F j, Y h:i A') : null,
            "date_denied" => $this["date_denied"] ?? null,
            "date_denied_human" => ($this["date_denied"] ?? null) ? Carbon::parse($this["date_denied"])->format('F j, Y h:i A') : null,
            "employee_name" => $user?->employee?->fullname_first ?? "SYSTEM ADMINISTRATOR",
            "employee_position" => $user?->employee?->current_position_name ?? "SYSTEM ADMINISTRATOR",
            "user_name" => $user?->name ?? "SYSTEM ADMINISTRATOR",
            "employee_signature" => ($this["status"] ?? null) === ApprovalStatus::APPROVED->value ? $user?->employee?->digital_signature?->base64 : null
        ];
    }
}

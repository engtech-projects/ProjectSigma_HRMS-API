<?php

namespace App\Http\Resources;

use App\Models\Users;
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
        $user = Users::with('employee')->where('id', $this["user_id"])->first();
        $employee = null;
        if ($user) {
            $employee = $user->employee ? new EmployeeUserResource($user->employee) : null;
        }
        return [
            "type" => $this["type"],
            "status" => $this["status"] ?? null,
            "user_id" => $this["user_id"] ?? null,
            "remarks" => $this["remarks"] ?? null,
            "date_approved" => $this["date_approved"] ?? null,
            "date_approved_human" => ($this["date_approved"] ?? null) ? Carbon::parse($this["date_approved"])->format('F j, Y h:i A') : null,
            "date_denied" => $this["date_denied"] ?? null,
            "date_denied_human" => ($this["date_denied"] ?? null) ? Carbon::parse($this["date_denied"])->format('F j, Y h:i A') : null,
            "employee" => $employee
        ];
    }
}

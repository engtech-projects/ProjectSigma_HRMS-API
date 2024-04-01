<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApprovalAttributeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = User::with('employee')->where('id', $this["user_id"])->first();
        $employee = $user->employee ? new EmployeeUserResource($user->employee) : null;
        return [
            "type" => $this["type"],
            "status" => $this["status"],
            "userselector" => array_key_exists("userselector", $this->resource) ? $this['userselector'] : null,
            "remarks" => $this["remarks"],
            "employee" => $employee
        ];
    }
}

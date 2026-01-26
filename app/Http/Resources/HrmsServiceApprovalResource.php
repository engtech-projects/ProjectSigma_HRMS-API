<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HrmsServiceApprovalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this['id'],
            'form' => $this['form'],
            'module' => $this['module'],
            'approvals' => HrmsServiceApprovalAttributeResource::collection($this['approvals'])
        ];
    }
}

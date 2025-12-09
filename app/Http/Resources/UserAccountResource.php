<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Used to Display User Accoutn Details With Accessibility names
        // If Need Employee Details Please Use UserEmployee Resource
        return [
            ...parent::toArray($request),
            "accessibility_names" => $this->accessibility_names,
        ];
    }
}

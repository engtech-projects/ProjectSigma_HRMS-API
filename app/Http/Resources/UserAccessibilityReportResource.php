<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAccessibilityReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->employee?->fullname_last,
            'accessibilities' => collect($this->accessibility_names ?? [])
            ->sort(SORT_NATURAL, SORT_FLAG_CASE)
            ->implode(",\r\n"),
        ];
    }
}

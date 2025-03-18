<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class JobApplicantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $positions = collect($this->manpower)
        ->map(function ($manpower) {
            return [
                'name' => $manpower->position->name,
            ];
        });

        return [
            ...parent::toArray($request),
            "remarks" => $this->remarks ?? "",
            "created_at" => $this->created_at ? Carbon::parse($this->created_at)->format('F j, Y') : null,
            "position" => $this->manpower ? $positions : "",
        ];
    }
}

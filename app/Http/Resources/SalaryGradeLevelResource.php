<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalaryGradeLevelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "salary_grade_level" => $this->salary_grade_level,
            "salary_grade_step" => SalaryGradeStepResource::collection($this->whenLoaded('salary_grade_step')) ?? [],
        ];
        //return parent::toArray($request);
    }
}

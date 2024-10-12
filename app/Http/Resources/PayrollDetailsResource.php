<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PayrollDetailsResource extends JsonResource
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
            "employee" => new EmployeeSummaryResource($this->employee),
            'cuttoff_start_human' => $this->payroll_record->cutoff_start_human ?? null,
            'cuttoff_end_human' => $this->payroll_record->cuttoff_end_human ?? null,
            'total_days_worked' => intval($this->regular_hours / 8),
            "employee_grade" => $this->employee->employee_internal()?->currentOnDate($this->payroll_date)?->first(),
            "adjustments" => $this->adjustments,
            "deductions" => $this->deductions,
            "charges" => $this->charges,
        ];
    }
}

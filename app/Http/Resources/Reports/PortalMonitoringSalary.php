<?php

namespace App\Http\Resources\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PortalMonitoringSalary extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "project_name" => $this["project_name"],
            "project_identifier" => $this["project_identifier"],
            "pay_basic" => $this["salaries"]["charging_pay_basic"] ?? 0,
            "number_of_personnel_basic_pay" => $this["salaries"]["charging_pay_basic_personnel"] ?? 0,
            "pay_overtime" => $this["salaries"]["charging_pay_overtime"] ?? 0,
            "number_of_personnel_overtime_pay" => $this["salaries"]["charging_pay_overtime_personnel"] ?? 0,
            "pay_sunday" => $this["salaries"]["charging_pay_sunday"] ?? 0,
            "number_of_personnel_sunday_pay" => $this["salaries"]["charging_pay_sunday_personnel"] ?? 0,
            "pay_allowance" => $this["salaries"]["charging_pay_allowance"] ?? 0,
            "number_of_personnel_allowance_pay" => $this["salaries"]["charging_pay_allowance_personnel"] ?? 0,
            "pay_regular_holiday_pay" => $this["salaries"]["charging_pay_regular_holiday"] ?? 0,
            "number_of_personnel_regular_holiday_pay" => $this["salaries"]["charging_pay_regular_personnel"] ?? 0,
            "pay_special_holiday" => $this["salaries"]["charging_pay_special_holiday"] ?? 0,
            "number_of_personnel_special_holiday" => $this["salaries"]["charging_pay_special_holiday_personnel"] ?? 0,
        ];
    }
}

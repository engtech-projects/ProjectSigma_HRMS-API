<?php

namespace App\Http\Services\ApiServices;

use App\Enums\GroupType;
use App\Enums\SigmaServices\AccountingPayrollParticulars;
use App\Http\Resources\RequestPayrollSummaryResource;
use App\Models\Department;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AccountingSecretkeyService
{
    protected $apiUrl;
    protected $authToken; // SECRET KEY

    public function __construct()
    {
        $this->authToken = config('services.sigma.secret_key');
        $this->apiUrl = config('services.url.accounting_api');
    }

    public function submitPayrollRequest($salaryDisbursementRequest)
    {
        $sdr = new RequestPayrollSummaryResource($salaryDisbursementRequest);
        $sdrArray = $sdr->toArray(new Request());
        $details = $sdrArray["summary"];
        // Log::info($sdrArray);
        // Log::info($details->toArray(new Request()));
        $payload = [
            "payee" => "MAYBANK",
            "amount" => "",
            "details" => $details->flatMap(function ($detail, $stakeholder) {
                $datas = [];
                $detail = $detail->toArray(new Request())['summary'];
                // Log::info($stakeholder);
                // Log::info($detail);
                $chargingType = $detail["charging_type_name"];
                //  CA/OD/LOANS AGGREGATION
                $employeeSss = $detail["deduct_sss_employee_contribution"] + $detail["deduct_sss_employee_compensation"] + $detail["deduct_sss_employee_wisp"];
                if ($employeeSss > 0) {
                    $datas[] = [
                        'particular' => AccountingPayrollParticulars::SSS_PREMIUM_PAYABLE->value,
                        'amount' => $employeeSss,
                    ];
                }
                if ($detail["deduct_philhealth_employee_contribution"] > 0) {
                    $datas[] = [
                        'particular' => AccountingPayrollParticulars::PHIC_PREMIUM_PAYABLE->value,
                        'amount' => $detail["deduct_philhealth_employee_contribution"],
                    ];
                }
                if ($detail["deduct_pagibig_employee_contribution"] > 0) {
                    $datas[] = [
                        'particular' => AccountingPayrollParticulars::HDMF_PREMIUM_PAYABLE->value,
                        'amount' => $detail["deduct_pagibig_employee_contribution"],
                    ];
                }
                // BASIC and OT Pay Aggregation
                if ($chargingType == Project::class) {
                    if ($detail['charging_pay_basic']) {
                        $datas[] = [
                            'particular' => AccountingPayrollParticulars::SALARY_AND_WAGES_BASIC_PAY->value,
                            'amount' => $detail["charging_pay_basic"],
                            'stakeholder' => $stakeholder,
                            'stakeholder_type' => 'Project',
                        ];
                    }
                    if ($detail['charging_pay_overtime']) {
                        $datas[] = [
                            'particular' => AccountingPayrollParticulars::SALARY_AND_WAGES_OT_PAY->value,
                            'amount' => $detail["charging_pay_overtime"],
                            'stakeholder' => $stakeholder,
                            'stakeholder_type' => 'Project',
                        ];
                    }
                } elseif ($chargingType == Department::class) {
                    if ($detail['charging_pay_basic']) {
                        $datas[] = [
                            'particular' => AccountingPayrollParticulars::SALARY_AND_WAGES_BASIC_PAY_OFFICE->value,
                            'amount' => $detail["charging_pay_basic"],
                            'stakeholder' => $stakeholder,
                            'stakeholder_type' => 'Department',
                        ];
                    }
                    if ($detail['charging_pay_overtime']) {
                        $datas[] = [
                            'particular' => AccountingPayrollParticulars::SALARY_AND_WAGES_OT_PAY_OFFICE->value,
                            'amount' => $detail["charging_pay_overtime"],
                            'stakeholder' => $stakeholder,
                            'stakeholder_type' => 'Department',
                        ];
                    }
                }
                return $datas;
            }),
        ];
        // AGGREGATE DEDUCTION TOTALS
        $sss = collect($payload['details'])->where('particular', AccountingPayrollParticulars::SSS_PREMIUM_PAYABLE->value)->sum('amount');
        $pagibig = collect($payload['details'])->where('particular', AccountingPayrollParticulars::HDMF_PREMIUM_PAYABLE->value)->sum('amount');
        $philhealth = collect($payload['details'])->where('particular', AccountingPayrollParticulars::PHIC_PREMIUM_PAYABLE->value)->sum('amount');
        // REMOVE DEDUCTIONS
        $payload["details"] = collect($payload["details"])->filter(function ($detail) {
            return !in_array($detail['particular'], [
                AccountingPayrollParticulars::SSS_PREMIUM_PAYABLE->value,
                AccountingPayrollParticulars::HDMF_PREMIUM_PAYABLE->value,
                AccountingPayrollParticulars::PHIC_PREMIUM_PAYABLE->value,
            ]);
        })->values()->all();
        // ADD DEDUCTION AGGREGATES
        if  ($sss > 0){
            $payload["details"][] = [
                'particular' => AccountingPayrollParticulars::SSS_PREMIUM_PAYABLE->value,
                'amount' => $sss,
            ];
        }
        if ($pagibig > 0) {
            $payload["details"][] = [
                'particular' => AccountingPayrollParticulars::HDMF_PREMIUM_PAYABLE->value,
                'amount' => $pagibig,
            ];
        }
        if ($philhealth > 0) {
            $payload["details"][] = [
                'particular' => AccountingPayrollParticulars::PHIC_PREMIUM_PAYABLE->value,
                'amount' => $philhealth,
            ];
        }
        Log::info($payload);
        return true;
        // $response = Http::withToken($this->authToken)
        //     ->withBody(json_encode($sdrArray), 'application/json')
        //     ->acceptJson()
        //     ->post($this->apiUrl.'/api/submit-payroll-prf');
        // return $response->successful();
    }
}

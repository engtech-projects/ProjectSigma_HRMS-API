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
                $summary = $detail->toArray(new Request())['summary'];
                $chargingType = $summary["charging_type_name"];
                $payrollData = $detail->toArray(new Request())['data']["details"];
                // Log::info($stakeholder);
                // Log::info($summary);
                // BASIC and OT Pay Aggregation
                if ($chargingType == Project::class) {
                    if ($summary['charging_pay_basic']) {
                        $datas[] = [
                            'particular' => AccountingPayrollParticulars::SALARY_AND_WAGES_BASIC_PAY->value,
                            'amount' => $summary["charging_pay_basic"],
                            'stakeholder' => $stakeholder,
                            'stakeholder_type' => 'Project',
                        ];
                    }
                    if ($summary['charging_pay_overtime']) {
                        $datas[] = [
                            'particular' => AccountingPayrollParticulars::SALARY_AND_WAGES_OT_PAY->value,
                            'amount' => $summary["charging_pay_overtime"],
                            'stakeholder' => $stakeholder,
                            'stakeholder_type' => 'Project',
                        ];
                    }
                } elseif ($chargingType == Department::class) {
                    if ($summary['charging_pay_basic']) {
                        $datas[] = [
                            'particular' => AccountingPayrollParticulars::SALARY_AND_WAGES_BASIC_PAY_OFFICE->value,
                            'amount' => $summary["charging_pay_basic"],
                            'stakeholder' => $stakeholder,
                            'stakeholder_type' => 'Department',
                        ];
                    }
                    if ($summary['charging_pay_overtime']) {
                        $datas[] = [
                            'particular' => AccountingPayrollParticulars::SALARY_AND_WAGES_OT_PAY_OFFICE->value,
                            'amount' => $summary["charging_pay_overtime"],
                            'stakeholder' => $stakeholder,
                            'stakeholder_type' => 'Department',
                        ];
                    }
                }
                //  CA/REMITTANCES AGGREGATION
                $employeeSss = $summary["deduct_sss_employee_contribution"] + $summary["deduct_sss_employee_compensation"] + $summary["deduct_sss_employee_wisp"];
                if ($employeeSss > 0) {
                    $datas[] = [
                        'particular' => AccountingPayrollParticulars::SSS_PREMIUM_PAYABLE->value,
                        'amount' => $employeeSss,
                    ];
                }
                if ($summary["deduct_philhealth_employee_contribution"] > 0) {
                    $datas[] = [
                        'particular' => AccountingPayrollParticulars::PHIC_PREMIUM_PAYABLE->value,
                        'amount' => $summary["deduct_philhealth_employee_contribution"],
                    ];
                }
                if ($summary["deduct_pagibig_employee_contribution"] > 0) {
                    $datas[] = [
                        'particular' => AccountingPayrollParticulars::HDMF_PREMIUM_PAYABLE->value,
                        'amount' => $summary["deduct_pagibig_employee_contribution"],
                    ];
                }
                if ($summary["deduct_withholdingtax"] > 0) {
                    $datas[] = [
                        'particular' => AccountingPayrollParticulars::EWTC->value,
                        'amount' => $summary["deduct_withholdingtax"],
                    ];
                }
                if ($summary["deduct_cashadvance"] > 0) {
                    $datas[] = [
                        'particular' => AccountingPayrollParticulars::ADVANCES_TO_OFFICERS_AND_EMPLOYEES->value,
                        'amount' => $summary["deduct_cashadvance"],
                    ];
                }
                // OD/LOANS AGGREGATION
                foreach($payrollData as $pDetail) {
                    foreach ($pDetail->otherDeductionPayments as $otherDeductionPayment) {
                        $datas[] = [
                            'particular' => $otherDeductionPayment->deduction->otherdeduction->otherdeduction_name,
                            'amount' => $otherDeductionPayment->amount,
                            "temp_type" => 'otherdeduction',
                        ];
                    }
                    foreach ($pDetail->loanPayments as $loanPayment) {
                        $datas[] = [
                            'particular' => $loanPayment->deduction->loan->name,
                            'amount' => $loanPayment->amount,
                            "temp_type" => 'loan',
                        ];
                    }
                }
                // Log::info($payrollData);
                return $datas;
            }),
        ];
        // AGGREGATE DEDUCTION TOTALS
        $sss = collect($payload['details'])->where('particular', AccountingPayrollParticulars::SSS_PREMIUM_PAYABLE->value)->sum('amount');
        $pagibig = collect($payload['details'])->where('particular', AccountingPayrollParticulars::HDMF_PREMIUM_PAYABLE->value)->sum('amount');
        $philhealth = collect($payload['details'])->where('particular', AccountingPayrollParticulars::PHIC_PREMIUM_PAYABLE->value)->sum('amount');
        $wtax = collect($payload['details'])->where('particular', AccountingPayrollParticulars::EWTC->value)->sum('amount');
        $cashAdvance = collect($payload['details'])->where('particular', AccountingPayrollParticulars::ADVANCES_TO_OFFICERS_AND_EMPLOYEES->value)->sum('amount');
        $loans = collect($payload['details'])->where('temp_type', 'loan')->values()->all();
        $otherDeductions = collect($payload['details'])->where('temp_type', 'otherdeduction')->values()->all();
        Log::info($loans);
        Log::info($otherDeductions);
        // REMOVE DEDUCTIONS
        $payload["details"] = collect($payload["details"])->filter(function ($detail) {
            return !(in_array($detail['particular'], [
                AccountingPayrollParticulars::SSS_PREMIUM_PAYABLE->value,
                AccountingPayrollParticulars::HDMF_PREMIUM_PAYABLE->value,
                AccountingPayrollParticulars::PHIC_PREMIUM_PAYABLE->value,
                AccountingPayrollParticulars::EWTC->value,
                AccountingPayrollParticulars::ADVANCES_TO_OFFICERS_AND_EMPLOYEES->value,
            ]) || in_array($detail['temp_type'] ?? null, ['loan', 'otherdeduction']));
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
        if ($wtax > 0) {
            $payload["details"][] = [
                'particular' => AccountingPayrollParticulars::EWTC->value,
                'amount' => $wtax,
            ];
        }
        if ($cashAdvance > 0) {
            $payload["details"][] = [
                'particular' => AccountingPayrollParticulars::ADVANCES_TO_OFFICERS_AND_EMPLOYEES->value,
                'amount' => $cashAdvance,
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

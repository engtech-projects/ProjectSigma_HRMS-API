<?php

namespace App\Http\Services\ApiServices;

use App\Enums\ReleaseType;
use App\Enums\SigmaServices\AccountingPayrollParticulars;
use App\Http\Resources\RequestPayrollSummaryResource;
use App\Models\Department;
use App\Models\Project;
use App\Models\SigmaServices\AccountingParticular;
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
        $stopError = false;
        $dataErrors = "";
        $sdr = new RequestPayrollSummaryResource($salaryDisbursementRequest);
        $sdrArray = $sdr->toArray(new Request());
        $details = $sdrArray["summary"];
        $atmRemark = $salaryDisbursementRequest->release_type == ReleaseType::ATM ? "WITH ATM" : "WITHOUT ATM";
        $net = 0;
        $payload = [
            "requested_by" => $salaryDisbursementRequest->created_by,
            "remarks" => "PAYMENT FOR " . $salaryDisbursementRequest->payroll_type . " PAYROLL " . $atmRemark . " FOR THE PAYROLL PERIOD " . $salaryDisbursementRequest->payroll_date_human . ".",
            "payroll_summary_id" => $salaryDisbursementRequest->id,
            "payee" => "MAYBANK",
            "amount" => "",
            "details" => $details->flatMap(function ($detail, $stakeholder) use (&$net) {
                $datas = [];
                $summary = $detail->toArray(new Request())['summary'];
                $chargingType = $summary["charging_type_name"];
                $payrollData = $detail->toArray(new Request())['data']["details"];
                // BASIC and OT Pay Aggregation
                $net += $summary["charging_net_pay"] ?? 0;
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
                foreach ($payrollData as $pDetail) {
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
                return $datas;
            }),
        ];
        // AGGREGATE DEDUCTION TOTALS
        $tempAllDeductionDetails = [];
        $loanParticularTerms = AccountingParticular::where('type', 'loan')->get();
        $loanParticularTerms = $loanParticularTerms->flatMap(function ($particularTerm) {
            return [
                strtolower($particularTerm->local_particular_name) => $particularTerm->accounting_particular_name
            ];
        });
        $odParticularTerms = AccountingParticular::where('type', 'other deduction')->get();
        $odParticularTerms = $odParticularTerms->flatMap(function ($particularTerm) {
            return [
                strtolower($particularTerm->local_particular_name) => $particularTerm->accounting_particular_name
            ];
        });
        $sss = collect($payload['details'])->where('particular', AccountingPayrollParticulars::SSS_PREMIUM_PAYABLE->value)->sum('amount');
        $pagibig = collect($payload['details'])->where('particular', AccountingPayrollParticulars::HDMF_PREMIUM_PAYABLE->value)->sum('amount');
        $philhealth = collect($payload['details'])->where('particular', AccountingPayrollParticulars::PHIC_PREMIUM_PAYABLE->value)->sum('amount');
        $wtax = collect($payload['details'])->where('particular', AccountingPayrollParticulars::EWTC->value)->sum('amount');
        $cashAdvance = collect($payload['details'])->where('particular', AccountingPayrollParticulars::ADVANCES_TO_OFFICERS_AND_EMPLOYEES->value)->sum('amount');
        $loanProblems = [];
        $loans = collect($payload['details'])->where('temp_type', 'loan')->map(function ($loan) use ($loanParticularTerms, &$loanProblems) {
            $loanParticularLower = strtolower($loan['particular']);
            if (!array_key_exists($loanParticularLower, $loanParticularTerms->toArray()) && !in_array($loanParticularLower, $loanProblems)) {
                $loanProblems[] = $loanParticularLower;
            }
            return [
                'particular' => $loanParticularTerms[$loanParticularLower] ?? 'Unknown',
                'amount' => $loan['amount'],
            ];
        })->values()->all();
        $otherDeductionProblems = [];
        $otherDeductions = collect($payload['details'])->where('temp_type', 'otherdeduction')->map(function ($otherDeduction) use ($odParticularTerms, &$otherDeductionProblems) {
            $otherDeductionParticularLower = strtolower($otherDeduction['particular']);
            if (!array_key_exists($otherDeductionParticularLower, $odParticularTerms->toArray()) && !in_array($otherDeductionParticularLower, $otherDeductionProblems)) {
                $otherDeductionProblems[] = $otherDeductionParticularLower;
            }
            return [
                'particular' => $odParticularTerms[$otherDeductionParticularLower] ?? 'Unknown',
                'amount' => $otherDeduction['amount'],
            ];
        })->values()->all();
        if ($loanProblems) {
            // Create and SendNotification
            $stopError = true;
            $dataErrors .= 'Loans not set: ' . implode(', ', (array)$loanProblems);
        }
        if ($otherDeductionProblems) {
            // Create and SendNotification
            $stopError = true;
            $dataErrors .= 'Other Deductions not set: ' . implode(', ', (array)$otherDeductionProblems);
        }
        if ($stopError) {
            return [
                "success" => false,
                "message" => "Particulars not set: " . $dataErrors
            ];
        }
        // REMOVE TEMP DEDUCTIONS
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
        if ($sss > 0) {
            $tempAllDeductionDetails[] = [
                'particular' => AccountingPayrollParticulars::SSS_PREMIUM_PAYABLE->value,
                'amount' => $sss,
            ];
        }
        if ($pagibig > 0) {
            $tempAllDeductionDetails[] = [
                'particular' => AccountingPayrollParticulars::HDMF_PREMIUM_PAYABLE->value,
                'amount' => $pagibig,
            ];
        }
        if ($philhealth > 0) {
            $tempAllDeductionDetails[] = [
                'particular' => AccountingPayrollParticulars::PHIC_PREMIUM_PAYABLE->value,
                'amount' => $philhealth,
            ];
        }
        if ($wtax > 0) {
            $tempAllDeductionDetails[] = [
                'particular' => AccountingPayrollParticulars::EWTC->value,
                'amount' => $wtax,
            ];
        }
        if ($cashAdvance > 0) {
            $tempAllDeductionDetails[] = [
                'particular' => AccountingPayrollParticulars::ADVANCES_TO_OFFICERS_AND_EMPLOYEES->value,
                'amount' => $cashAdvance,
            ];
        }
        $tempAllDeductionDetails = array_merge($tempAllDeductionDetails, $loans);
        $tempAllDeductionDetails = array_merge($tempAllDeductionDetails, $otherDeductions);
        // SUM LOANS AND OTHER DEDUCTIONS BASED ON PARTICULAR NAME
        $tempAllDeductionDetails = collect($tempAllDeductionDetails)->groupBy('particular')->map(function ($group) {
            return [
                'particular' => $group->first()['particular'],
                'amount' => $group->sum('amount'),
            ];
        })->values()->all();
        $payload["details"] = array_merge($payload["details"], $tempAllDeductionDetails);
        $payload["amount"] = round($net, 2);
        $payload["details"][] = [
            'particular' => AccountingPayrollParticulars::PAYEE->value,
            'amount' => round($net, 2),
        ];
        // Log::info($payload);
        $response = Http::withToken($this->authToken)
            ->withBody(json_encode($payload), 'application/json')
            ->acceptJson()
            ->post($this->apiUrl.'/api/sigma/payroll/create-request');
        if ($response->failed()) {
            Log::error('Accounting API Error: ' . $response->body());
            return [
                "success" => false,
                "message" => "Failed to submit payroll request to accounting API.",
                "error" => $response->body(),
            ];
        }
        //Log::info($response);
        return $response;
        // return true;
    }
}

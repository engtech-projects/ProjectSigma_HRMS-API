<?php

namespace App\Http\Services\ApiServices;

use App\Enums\GroupType;
use App\Http\Resources\RequestPayrollSummaryResource;
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
            "details" => $details->flatMap(function ($detail) {
                Log::info($detail->toArray(new Request()));
                $datas = [];
                // if ($detail["charging_type"] == GroupType::PROJECT->value) {
                //     if ($detail['charging_pay_basic']) {
                //         $datas[] = [
                //             'particular' => '',
                //             'amount' => $detail["amount"],
                //             'stakeholder' => $detail["stakeholder"],
                //             'stakeholder_type' => $detail["stakeholder_type"],
                //         ];
                //     }
                //     if ($detail['charging_pay_overtime']) {
                //         $datas[] = [
                //             'particular' => '',
                //             'amount' => $detail["amount"],
                //             'stakeholder' => $detail["stakeholder"],
                //             'stakeholder_type' => $detail["stakeholder_type"],
                //         ];
                //     }
                // } elseif ($detail["charging_type"] == GroupType::DEPARTMENT->value) {
                //     if ($detail['charging_pay_basic']) {
                //         $datas[] = [
                //             'particular' => '',
                //             'amount' => $detail["amount"],
                //             'stakeholder' => $detail["stakeholder"],
                //             'stakeholder_type' => $detail["stakeholder_type"],
                //         ];
                //     }
                //     if ($detail['charging_pay_overtime']) {
                //         $datas[] = [
                //             'particular' => '',
                //             'amount' => $detail["amount"],
                //             'stakeholder' => $detail["stakeholder"],
                //             'stakeholder_type' => $detail["stakeholder_type"],
                //         ];
                //     }
                // }
                return $datas;
            }),
        ];
        Log::info($payload);
        return true;
        // $response = Http::withToken($this->authToken)
        //     ->withBody(json_encode($sdrArray), 'application/json')
        //     ->acceptJson()
        //     ->post($this->apiUrl.'/api/submit-payroll-prf');
        // return $response->successful();
    }
}

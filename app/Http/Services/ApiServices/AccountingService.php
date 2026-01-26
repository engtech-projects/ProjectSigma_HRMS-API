<?php

namespace App\Http\Services\ApiServices;

class AccountingService
{
    protected $apiUrl;
    protected $authToken;

    public function __construct($authToken)
    {
        $this->authToken = $authToken;
        $this->apiUrl = config('services.url.accounting_api');
    }

    public function submitPayrollRequest($token)
    {
        return true;
    }
}

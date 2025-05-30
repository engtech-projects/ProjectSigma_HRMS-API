<?php

namespace App\Http\Services\ApiServices;

class InventorySecretkeyService
{
    protected $apiUrl;
    protected $authToken; // SECRET KEY

    public function __construct()
    {
        $this->authToken = config('services.sigma.secret_key');
        $this->apiUrl = config('services.url.inventory_api');
    }
}

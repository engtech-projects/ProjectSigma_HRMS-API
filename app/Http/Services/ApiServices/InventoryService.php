<?php

namespace App\Http\Services\ApiServices;

class InventoryService
{
    protected $apiUrl;
    protected $authToken;

    public function __construct($authToken)
    {
        $this->authToken = $authToken;
        $this->apiUrl = config('services.url.inventory_api');
    }
}

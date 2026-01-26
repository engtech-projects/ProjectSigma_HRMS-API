<?php

namespace App\Http\Services\ApiServices;

use App\Models\Project;

class ProjectMonitoringService
{
    protected $apiUrl;
    protected $authToken;

    public function __construct($authToken)
    {
        $this->authToken = $authToken;
        $this->apiUrl = config('services.url.projects_api');
    }
    // SYNC FUNCTIONS MOVED TO PROJECT MONITORING SECRET KEY SERVICE
}

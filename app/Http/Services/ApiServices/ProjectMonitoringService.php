<?php

namespace App\Http\Services\ApiServices;

use App\Models\Project;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProjectMonitoringService
{
    protected $apiUrl;
    protected $authToken;

    public function __construct($authToken)
    {
        $this->authToken = $authToken;
        $this->apiUrl = config('services.url.projects_api');
    }

    public function syncAll()
    {
        $syncProject = $this->syncProjects();
        return $syncProject;
    }
    public function syncProjects()
    {
        $projects = $this->getAllProjects();
        collect($projects)->map(function ($project) {
            return [
                "id" => $project['id'],
                "project_monitoring_id" => $project['id'],
                "project_code" => $project['code'],
                "status" => $project['status'],
            ];
        });
        Project::upsert(
            $projects,
            [
                'id'
            ],
            [
                'project_monitoring_id',
                'project_code',
                'status',
            ]
        );
        return true;
    }
    public function getAllProjects()
    {
        $response = Http::withToken($this->authToken)
            ->withUrlParameters([
                "stage" => "awarded",
                "status" => "ongoing",
                "paginate" => false,
                "sort" => "asc"
            ])
            ->acceptJson()
            ->get($this->apiUrl.'/api/projects');
        if (! $response->successful()) {
            return [];
        }
        return $response->json();
    }
}

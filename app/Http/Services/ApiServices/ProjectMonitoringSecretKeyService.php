<?php

namespace App\Http\Services\ApiServices;

use App\Models\Project;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProjectMonitoringSecretKeyService
{
    protected $apiUrl;
    protected $authToken;

    public function __construct()
    {
        $this->authToken = config('services.sigma.secret_key');
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
        $projects = collect($projects)->map(function ($project) {
            return [
                "id" => $project['id'],
                "project_monitoring_id" => $project['id'],
                "project_code" => $project['code'],
                "status" => $project['status'],
            ];
        })->toArray();
        Project::upsert(
            $projects,
            [
                'id',
                'project_monitoring_id',
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
            ->get($this->apiUrl.'/sigma/sync-list/projects');
        if (! $response->successful()) {
            Log::channel('project_monitoring')->error('Project API sync failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'url' => $this->apiUrl.'/sigma/sync-list/projects'
            ]);
            return [];
        }
        return $response->json()["data"];
    }
}

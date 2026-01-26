<?php

namespace App\Http\Services\ApiServices;

use App\Models\Project;
use DateTime;
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
                "project_code" => $project['code'],
                "status" => $project['status'],
                "created_at" => new DateTime($project['created_at']),
                "updated_at" => new DateTime($project['updated_at']),
                "deleted_at" => $project["deleted_at"] ? new DateTime($project['deleted_at']) : null,
            ];
        })->toArray();
        Project::upsert(
            $projects,
            [
                'id',
            ],
            [
                'project_code',
                'status',
                'created_at',
                'updated_at',
                'deleted_at',
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
            ->get($this->apiUrl.'/api/sigma/sync-list/projects');
        if (! $response->successful()) {
            Log::channel('project_monitoring')->error('Project API sync failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'url' => $this->apiUrl.'/api/sigma/sync-list/projects'
            ]);
            return [];
        }
        return $response->json()["data"];
    }
}

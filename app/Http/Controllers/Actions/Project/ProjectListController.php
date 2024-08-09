<?php

namespace App\Http\Controllers\Actions\Project;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class ProjectListController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $token = $request->bearerToken();
        $url = config()->get('services.url.projects_api');
        $response = Http::acceptJson()->withToken($token)->get($url . 'api/projects?completion_status=ongoing');
        $projectsApiProjects = $response->json('data');
        if ($response->successful()) {
            foreach ($projectsApiProjects as $project) {
                $model = Project::where('project_monitoring_id', $project["id"])->first();
                if ($model) {
                    $model->update([
                        "project_monitoring_id" => $project['id'],
                        "project_code" => $project["project_code"],
                        "status" => $project["status"]
                    ]);
                } else {
                    Project::create([
                        "project_monitoring_id" => $project['id'],
                        "project_code" => $project["project_code"],
                        "status" => $project["status"]
                    ]);
                }
            }
        }
        $result = collect(Project::all())->map(function ($project) use ($projectsApiProjects) {
            $project["projects"] = collect($projectsApiProjects)->firstWhere("id", $project["project_monitoring_id"]);
            return $project;
        })->reject(function ($project) {
            return $project["projects"] == null;
        });

        return new JsonResponse([
            'success' => true,
            'message' => "Projects successfully updated.",
            'data' => $result
        ]);
    }
}

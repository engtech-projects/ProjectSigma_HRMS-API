<?php

namespace App\Http\Controllers\Actions\Project;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\PersonalAccessToken;

class ProjectListController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $token = $request->bearerToken();
        $url = config()->get('services.url.projects_api_url');
        $response = Http::acceptJson()->throw()->withToken($token)->withQueryParameters([
            'completion_status' => 'ongoing'
        ])->get($url . 'api/projects/');

        $projects = $response->json('data');
        if ($response->successful()) {
            foreach ($projects as $project) {
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

        return new JsonResponse([
            'success' => true,
            'message' => "Projects successfully updated.",
            'data' => $projects
        ]);
    }
}

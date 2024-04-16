<?php

namespace App\Http\Controllers\Actions\Project;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProjectListController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $token = $request->bearerToken();
        $url = config()->get('url.projects_api_url');
        $response = Http::acceptJson()->withToken($token)->withQueryParameters([
            'completion_status' => 'ongoing'
        ])->get($url);
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

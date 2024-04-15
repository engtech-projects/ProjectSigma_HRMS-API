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
        $newProjects = [];
        $projects = [
            [
                "id" => 6,
                "project_code" => "Code test 124",
                "contract_id" => "1100077",
                "contract_name" => "test name 1",
                "status" => "completed"
            ],
            [
                "id" => 7,
                "project_code" => "Code test 444",
                "contract_id" => "87343",
                "contract_name" => "test name 2",
                "status" => "completed"
            ],
            [
                "id" => 8,
                "project_code" => "Code test 667",
                "contract_id" => "76769000",
                "contract_name" => "test name 3",
                "status" => "completed"
            ],
            [
                "id" => 1,
                "project_code" => "Code test 657",
                "contract_id" => "123555",
                "contract_name" => "test name 4",
                "status" => "completed"
            ],
            [
                "id" => 10,
                "project_code" => "Code test 1255",
                "contract_id" => "45677",
                "contract_name" => "test name 5",
                "status" => "completed"
            ],
        ];

        $hrmsProjects = Project::get();
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
        return new JsonResponse([
            'success' => true,
            'message' => "Successfully fetched.",
            'hrms_project' => $hrmsProjects,
            'projects' => $projects,
            'result' => $newProjects
        ]);
    }
}

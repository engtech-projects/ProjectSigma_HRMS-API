<?php

namespace App\Http\Controllers\Actions\Project;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class ProjectListController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $allProjects = Project::all();
        return new JsonResponse([
            'success' => true,
            'message' => "Projects successfully updated.",
            'data' => $allProjects
        ]);
    }
}

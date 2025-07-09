<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectResource;
use App\Http\Services\ProjectService;
use Illuminate\Http\Request;

class ProjectListController extends Controller
{
    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $project = $this->projectService->getAll();
        return ProjectResource::collection($project)
        ->additional([
            'success' => true,
            'message' => 'Successfully fetched.',
        ]);
    }
}

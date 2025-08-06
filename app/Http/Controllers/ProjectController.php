<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\JsonResponse;
use App\Http\Services\ProjectService;
use App\Http\Resources\ProjectResource;

class ProjectController extends Controller
{
    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $project = $this->projectService->getAll();
        return ProjectResource::collection($project)
        ->additional([
            'success' => true,
            'message' => 'Successfully fetch.',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        return new JsonResponse([
            "success" => true,
            "message" => "Successfully fetched.",
            "data" => new ProjectResource($project),
        ], JsonResponse::HTTP_OK);
    }
}

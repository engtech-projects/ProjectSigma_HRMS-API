<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\JsonResponse;
use App\Http\Services\ProjectService;
use App\Http\Resources\ProjectResource;
use App\Utils\PaginateResourceCollection;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;

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
        $collection = collect(ProjectResource::collection($project));

        return new JsonResponse([
            "success" => true,
            "message" => "Successfully fetch.",
            "data" => PaginateResourceCollection::paginate(collect($collection), 15)
        ], JsonResponse::HTTP_OK);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        $this->projectService->create($request->validated());

        return new JsonResponse([
            "success" => true,
            "message" => "Successfully created.",
        ], JsonResponse::HTTP_CREATED);
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

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $this->projectService->update($request->validated(), $project);

        return new JsonResponse([
            "success" => true,
            "message" => "Successfully updated.",
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $this->projectService->delete($project);

        return new JsonResponse([
            "success" => true,
            "message" => "Successfully deleted.",
        ], JsonResponse::HTTP_OK);
    }
}

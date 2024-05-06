<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectResource;
use App\Http\Services\ProjectService;
use App\Utils\PaginateResourceCollection;
use Illuminate\Http\JsonResponse;
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
        $collection = collect(ProjectResource::collection($project));
        return new JsonResponse([
            "success" => true,
            "message" => "Successfully fetch.",
            "data" => PaginateResourceCollection::paginate(collect($collection), 15)
        ], JsonResponse::HTTP_OK);
    }
}

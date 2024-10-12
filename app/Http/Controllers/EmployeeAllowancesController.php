<?php

namespace App\Http\Controllers;

use App\Enums\AssignTypes;
use App\Http\Requests\FilterEmployeeAllowancesRequest;
use App\Models\EmployeeAllowances;
use App\Http\Requests\StoreEmployeeAllowancesRequest;
use App\Http\Requests\UpdateEmployeeAllowancesRequest;
use App\Http\Resources\AllowanceRecordsResource;
use App\Http\Resources\AllowanceRequestResource;
use Illuminate\Http\JsonResponse;
use App\Http\Services\EmployeeAllowanceService;
use App\Models\AllowanceRequest;
use App\Models\Department;
use App\Models\Project;
use App\Utils\PaginateResourceCollection;
use Carbon\Carbon;

class EmployeeAllowancesController extends Controller
{
    public const DEPARTMENT = "App\Models\Department";
    public const PROJECT = "App\Models\Project";

    protected $employeeAllowanceService;
    protected $employeeAllowanceRequestType = null;

    public function __construct(EmployeeAllowanceService $employeeAllowanceService)
    {
        $this->employeeAllowanceRequestType = request()->get('type');
        $this->employeeAllowanceService = $employeeAllowanceService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $main = $this->employeeAllowanceService->getAll();
        if (!is_null($main)) {
            return new JsonResponse([
                'success' => true,
                'message' => 'Successfully fetch all Allowance Requests.',
                'data' => PaginateResourceCollection::paginate(collect(AllowanceRequestResource::collection($main))),
            ], JsonResponse::HTTP_OK);
        }
        return new JsonResponse([
            'success' => false,
            'message' => 'No data found.',
        ], 404);
    }

    /**
     * Show resource.
     */
    public function viewAllowanceRecords(FilterEmployeeAllowancesRequest $request)
    {
        $valData = $request->validated();
        try {
            if ($valData) {
                $id = null;
                $group_type = $request["group_type"];
                $assignData = null;
                switch ($group_type) {
                    case AssignTypes::DEPARTMENT->value:
                        $id = $request["department_id"];
                        $group_type = EmployeeAllowancesController::DEPARTMENT;
                        $assignData = Department::find($id);
                        break;
                    case AssignTypes::PROJECT->value:
                        $id = $request["project_id"];
                        $group_type = EmployeeAllowancesController::PROJECT;
                        $assignData = Project::find($id);
                        break;
                }
                $allowance_date = $request["allowance_date"];
                $data = EmployeeAllowances::whereHas("allowance_request", function ($query) use ($id, $group_type, $allowance_date) {
                    return $query->where([
                        'charge_assignment_id' => $id,
                        "charge_assignment_type" => $group_type,
                    ])
                    ->whereDate("allowance_date", $allowance_date)
                    ->requestStatusApproved();
                })
                ->get();
                return new JsonResponse([
                    'success' => true,
                    'message' => 'Successfully fetch.',
                    'data' => [
                        "charging_assignment" => $assignData,
                        "charge_name" => $assignData->project_code ?? $assignData->department_name ,
                        "allowance_date" => Carbon::parse($allowance_date)->format("F j, Y"),
                        "employee_allowances" => AllowanceRecordsResource::collection($data)
                    ],
                ], JsonResponse::HTTP_OK);
            }
        } catch (\Throwable $th) {
            return new JsonResponse([
                'success' => false,
                'error' => $th->getMessage(),
                'message' => 'Failed fetch.',
            ], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeAllowancesRequest $request)
    {
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $main = AllowanceRequest::find($id);
        if (!is_null($main)) {
            return new JsonResponse([
                'success' => true,
                'message' => 'Successfully fetch.',
                'data' => new AllowanceRequestResource($main),
            ], JsonResponse::HTTP_OK);
        }
        return new JsonResponse([
            'success' => false,
            'message' => 'No data found.',
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeAllowancesRequest $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $main = EmployeeAllowances::find($id);
        if (!is_null($main)) {
            if ($main->delete()) {
                return new JsonResponse([
                    'success' => true,
                    'message' => 'Successfully delete.',
                ], JsonResponse::HTTP_OK);
            }
            return new JsonResponse([
                'success' => false,
                'message' => 'Failed delete.',
            ], 400);
        }
        return new JsonResponse([
            'success' => false,
            'message' => 'No data found.',
        ], 404);
    }
}

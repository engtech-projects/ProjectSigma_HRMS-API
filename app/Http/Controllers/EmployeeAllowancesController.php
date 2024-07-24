<?php

namespace App\Http\Controllers;

use App\Enums\AssignTypes;
use App\Enums\RequestStatusType;
use App\Http\Requests\FilterEmployeeAllowancesRequest;
use App\Models\EmployeeAllowances;
use App\Http\Requests\StoreEmployeeAllowancesRequest;
use App\Http\Requests\UpdateEmployeeAllowancesRequest;
use App\Http\Resources\AllowanceRecordsResource;
use App\Http\Resources\AllowanceRequestResource;
use App\Models\Employee;
use App\Models\InternalWorkExperience;
use Illuminate\Http\JsonResponse;
use App\Http\Services\EmployeeAllowanceService;
use App\Models\AllowanceRequest;
use App\Models\Department;
use App\Models\Project;
use App\Utils\PaginateResourceCollection;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
                $data = EmployeeAllowances::whereHas("allowance_request", function($query) use($id, $group_type, $allowance_date){
                    return $query->where([
                        'charge_assignment_id' => $id,
                        "charge_assignment_type" => $group_type,
                        "allowance_date"=> $allowance_date,
                    ])
                    ->requestStatusApproved();
                })
                ->get();
                return new JsonResponse([
                    'success' => true,
                    'message' => 'Successfully fetch.',
                    'data' => [
                        "charging_assignment" => $assignData,
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
        $valData = $request->validated();
        try {
            DB::beginTransaction();
            $allowanceReq = new AllowanceRequest;
            $allowanceReq->fill($valData);
            $allowanceReq->request_status = RequestStatusType::PENDING;
            $allowanceReq->created_by = auth()->user()->id;
            if ($valData["group_type"] == AssignTypes::DEPARTMENT->value) {
                $allowanceReq->charge_assignment_id = $valData["department_id"];
                $allowanceReq->charge_assignment_type = Department::class;
            } else {
                $allowanceReq->charge_assignment_id = $valData["project_id"];
                $allowanceReq->charge_assignment_type = Project::class;
            }
            $allowanceReq->save();
            $errorList = [];
            foreach ($valData["employees"] as $key) {
                $employee = Employee::with('current_employment.position.allowances')->find($key);
                if (!$employee->current_employment) {
                    $errorList[] = $employee->fullname_last . " is currently NOT EMPLOYED.";
                    continue;
                }
                if (!$employee->current_employment->position->allowances) {
                    $errorList[] = $employee->current_employment->position->name . " has no allowance setup.";
                    continue;
                }
                $allowanceRate = $employee->current_employment->position->allowances->amount;
                $allowanceReq->employee_allowances()->attach(
                    $key,
                    [
                    "allowance_amount" => $allowanceRate * $valData['allowance_days'],
                    "allowance_rate" => $allowanceRate,
                    "allowance_days" => $valData['allowance_days'],
                ]);
            }
            if (sizeof($errorList) > 0) {
                return new JsonResponse([
                    'success' => false,
                    'error' => "Failed to Generate Allowance",
                    'message' => implode("\n", $errorList),
                ], 400);
            }
            DB::commit();
        } catch (\Throwable $th) {
            return new JsonResponse([
                'success' => false,
                'error' => $th->getMessage(),
                'message' => 'Failed save.',
            ], 400);
        }
        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully save.',
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $main = EmployeeAllowances::find($id);
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

    public function myRequest()
    {
        $myRequest = $this->employeeAllowanceService->getAll();
        if ($myRequest->isEmpty()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'No data found.',
            ], JsonResponse::HTTP_OK);
        }
        return new JsonResponse([
            'success' => true,
            'message' => 'Manpower Request fetched.',
            'data' => PaginateResourceCollection::paginate(collect(AllowanceRequestResource::collection($myRequest)))
        ]);
    }

    /**
     * Show all requests to be approved/reviewed by current user
     */
    public function myApproval()
    {
        $myApproval = $this->employeeAllowanceService->getMyApprovals();
        if ($myApproval->isEmpty()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'No data found.',
            ], JsonResponse::HTTP_OK);
        }
        return new JsonResponse([
            'success' => true,
            'message' => 'Manpower Request fetched.',
            'data' => AllowanceRequestResource::collection($myApproval)
        ]);
    }
}

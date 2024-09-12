<?php

namespace App\Http\Controllers;

use App\Enums\AssignTypes;
use App\Enums\RequestStatusType;
use App\Http\Requests\AllowanceRequestGenerateDraftRequest;
use App\Models\AllowanceRequest;
use App\Http\Requests\StoreAllowanceRequestRequest;
use App\Http\Requests\UpdateAllowanceRequestRequest;
use App\Http\Resources\AllowanceRequestResource;
use App\Http\Services\Attendance\AttendanceService;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Project;
use App\Models\Users;
use App\Notifications\AllowanceRequestForApproval;
use App\Utils\PaginateResourceCollection;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AllowanceRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $main = AllowanceRequest::all();
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
     * Store a newly created resource in storage.
     */
    public function generateDraft(AllowanceRequestGenerateDraftRequest $request)
    {
        $valData = $request->validated();
        $generatedData = $valData;
        $employee_allowances = [];
        $errorList = [];
        $cutoffStart = Carbon::parse($valData['cutoff_start'])->startOfDay();
        $cutoffEnd = Carbon::parse($valData['cutoff_end'])->startOfDay();
        if ($valData["charging_type"] == AssignTypes::DEPARTMENT->value) {
            $generatedData["charge_name"] = Department::find($valData["department_id"])->department_name;
        } else {
            $generatedData["charge_name"] = Project::find($valData["project_id"])->project_code;
        }
        $generatedData["allowance_date_human"] = Carbon::parse($valData["allowance_date"])->format('F j, Y');
        $generatedData["cutoff_end_human"] = Carbon::parse($valData["cutoff_end"])->format('F j, Y');
        $generatedData["cutoff_start_human"] = Carbon::parse($valData["cutoff_start"])->format('F j, Y');
        foreach ($valData["employees"] as $key) {
            $employee = Employee::find($key);
            if (!$employee->current_employment) {
                $errorList[] = $employee->fullname_last . " is currently NOT EMPLOYED.";
                continue;
            }
            if (!$employee->current_employment->position->allowances) {
                $errorList[] = $employee->current_employment->position->name . " has no allowance setup.";
                continue;
            }
            $daysPresent = AttendanceService::allowanceAttendance($employee, $cutoffStart, $cutoffEnd);
            $allowanceRate = $employee->current_employment?->position?->allowances?->amount ?? 0;
            $employee_allowances[] = [
                "employee_id" => $employee->id,
                "employee_position" => $employee->current_employment->position->name,
                'fullname_first' => $employee->fullname_first,
                'fullname_last' => $employee->fullname_last,
                "allowance_amount" => $allowanceRate * $daysPresent,
                "allowance_rate" => $allowanceRate,
                "total_days" => $valData['total_days'],
                "allowance_days" => $daysPresent,
            ];
        }
        $generatedData["employee_allowances"] = $employee_allowances;
        if (sizeof($errorList) > 0) {
            return new JsonResponse([
                'success' => false,
                'error' => "Failed to Generate Allowance Draft",
                'message' => "Failed to Generate Allowance Draft" . implode("\n", $errorList),
            ], 400);
        }
        return new JsonResponse([
            'success' => true,
            'message' => 'Allowance draft generated.',
            'data' => $generatedData,
        ], JsonResponse::HTTP_OK);
    }
    public function store(StoreAllowanceRequestRequest $request)
    {
        $valData = $request->validated();
        $valData["request_status"] = RequestStatusType::PENDING;
        $valData["created_by"] = auth()->user()->id;
        if ($valData["charging_type"] == AssignTypes::DEPARTMENT->value) {
            $valData["charge_assignment_id"] = $valData["department_id"];
            $valData["charge_assignment_type"] = Department::class;
        } else {
            $valData["request_status"] = $valData["project_id"];
            $valData["request_status"] = Project::class;
        }
        try {
            DB::beginTransaction();
            $allowanceReq = AllowanceRequest::create($valData);
            foreach ($valData["employee_allowances"] as $employeeAllowance) {
                $allowanceReq->employee_allowances()->attach($employeeAllowance['employee_id'], $employeeAllowance);
            }
            DB::commit();
            if ($allowanceReq->getNextPendingApproval()) {
                Users::find($allowanceReq->getNextPendingApproval()['user_id'])->notify(new AllowanceRequestForApproval($allowanceReq));
            }
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
    public function show(AllowanceRequest $resource)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAllowanceRequestRequest $request, AllowanceRequest $resource)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AllowanceRequest $resource)
    {
        //
    }

    public function myRequest()
    {
        $myRequest = AllowanceRequest::with(['employee_allowances','charge_assignment'])
        ->where("created_by", auth()->user()->id)
        ->get();;
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
        $userId = auth()->user()->id;
        $result = AllowanceRequest::with(['employee_allowances', 'charge_assignment'])
            ->requestStatusPending()
            ->authUserPending()
            ->get();
        $myApproval = $result->filter(function ($item) use ($userId) {
            $nextPendingApproval = $item->getNextPendingApproval();
            return ($nextPendingApproval && $userId === $nextPendingApproval['user_id']);
        });
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

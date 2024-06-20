<?php

namespace App\Http\Controllers;

use App\Enums\AssignTypes;
use App\Enums\RequestStatusType;
use App\Http\Requests\FilterEmployeeAllowancesRequest;
use App\Models\EmployeeAllowances;
use App\Http\Requests\StoreEmployeeAllowancesRequest;
use App\Http\Requests\UpdateEmployeeAllowancesRequest;
use App\Models\Employee;
use App\Models\InternalWorkExperience;
use Illuminate\Http\JsonResponse;
use App\Http\Services\EmployeeAllowanceService;
use App\Models\AllowanceRequest;
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
        $main = EmployeeAllowances::with('charge_assignment')->get();
        if (!is_null($main)) {
            return new JsonResponse([
                'success' => true,
                'message' => 'Successfully fetch.',
                'data' => $main,
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
                switch ($group_type) {
                    case AssignTypes::DEPARTMENT->value:
                        $id = $request["department_id"];
                        $group_type = EmployeeAllowancesController::DEPARTMENT;
                        break;
                    case AssignTypes::PROJECT->value:
                        $id = $request["project_id"];
                        $group_type = EmployeeAllowancesController::PROJECT;
                        break;
                }
                $allowance_date = $request["allowance_date"];
                $data = EmployeeAllowances::with('charge_assignment')->where([
                    ['charge_assignment_id', $id],
                    ["charge_assignment_type", $group_type],
                    ["allowance_date", $allowance_date],
                ])->get();

                return new JsonResponse([
                    'success' => true,
                    'message' => 'Successfully fetch.',
                    'data' => $data,
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
            if ($valData) {
                DB::beginTransaction();
                foreach ($valData["employees"] as $key) {
                    $data = Employee::with('current_employment.position.allowances')->find($key);

                    if (!$data->current_employment) {
                        return new JsonResponse([
                            'success' => false,
                            'message' => 'User ' . $data->fullname_first . " not found as not a current employee",
                        ], 400);
                    }

                    if (!$data->current_employment) {
                        return new JsonResponse([
                            'success' => false,
                            'message' => 'Employee ' . $data->fullname_first . " doesn't have a position",
                        ], 400);
                    }

                    if ($data->current_employment) {
                        if ($data->current_employment->position_id) {

                            if ($data->current_employment->position->allowances == null) {
                                return new JsonResponse([
                                    'success' => false,
                                    'message' => 'No allowance amount found',
                                ], 400);
                            }

                            $data_amt = $data->current_employment->position->allowances->amount;
                            $employee_allowance = new EmployeeAllowances();
                            $allowance_request = new AllowanceRequest();
                            $type = $request["group_type"];
                            switch ($type) {
                                case AssignTypes::DEPARTMENT->value:
                                    $allowance_request->charge_assignment_type = EmployeeAllowancesController::DEPARTMENT;
                                    $allowance_request->charge_assignment_id = $request["department_id"];
                                    break;
                                case AssignTypes::PROJECT->value:
                                    $allowance_request->charge_assignment_type = EmployeeAllowancesController::PROJECT;
                                    $allowance_request->charge_assignment_id = $request["project_id"];
                                    break;
                            }
                            $allowance_request->allowance_date = $request["allowance_date"];
                            $allowance_request->allowance_amount = $data_amt;
                            $allowance_request->cutoff_start = $request["cutoff_start"];
                            $allowance_request->cutoff_end = $request["cutoff_end"];
                            $allowance_request->total_days = $request["total_days"];
                            $allowance_request->request_status = RequestStatusType::PENDING;
                            $allowance_request->approvals = $request["approvals"];
                            $allowance_request->save();
                            $employee_allowance->allowance_amount = $data_amt;
                            $employee_allowance->allowance_request_id = $allowance_request->id;
                            $employee_allowance->employee_id = $key;
                            $employee_allowance->allowance_rate = $request["allowance_rate"];
                            $employee_allowance->allowance_days = $request["allowance_days"];
                            $employee_allowance->created_by = auth()->user()->id;;
                            $employee_allowance->save();
                        }
                    }
                }
                DB::commit();

                return new JsonResponse([
                    'success' => true,
                    'message' => 'Successfully save.',
                ], JsonResponse::HTTP_OK);

            }
        } catch (\Throwable $th) {
            return new JsonResponse([
                'success' => false,
                'error' => $th->getMessage(),
                'message' => 'Failed save.',
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $main = EmployeeAllowances::with('charge_assignment')->find($id);
        if (!is_null($main)) {
            return new JsonResponse([
                'success' => true,
                'message' => 'Successfully fetch.',
                'data' => $main,
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
        // $valData = $request->validated();
        // try {
        //     if ($valData) {
        //         foreach ($request["employees"] as $key) {
        //             $data = Employee::with('current_employment.position.allowances')->find($key);
        //             if ($data->current_employment) {
        //                 if ($data->current_employment->position_id) {

        //                     if ($data->current_employment->position->allowances == null) {
        //                         return new JsonResponse([
        //                             'success' => false,
        //                             'message' => 'No amount found',
        //                         ], 400);
        //                     }

        //                     $data_amt = $data->current_employment->position->allowances->amount;
        //                     $employee_allowance = new EmployeeAllowances();
        //                     $type = $request["group_type"];
        //                     switch ($type) {
        //                         case AssignTypes::DEPARTMENT->value:
        //                             $employee_allowance->charge_assignment_type = EmployeeAllowancesController::DEPARTMENT;
        //                             $employee_allowance->charge_assignment_id = $request["department_id"];
        //                             break;
        //                         case AssignTypes::PROJECT->value:
        //                             $employee_allowance->charge_assignment_type = EmployeeAllowancesController::PROJECT;
        //                             $employee_allowance->charge_assignment_id = $request["project_id"];
        //                             break;
        //                     }
        //                     $employee_allowance->allowance_date = $request["allowance_date"];
        //                     $employee_allowance->cutoff_start = $request["cutoff_start"];
        //                     $employee_allowance->cutoff_end = $request["cutoff_end"];
        //                     $employee_allowance->total_days = $request["total_days"];
        //                     $employee_allowance->allowance_amount = $data_amt;
        //                     $employee_allowance->save();

        //                     return new JsonResponse([
        //                         'success' => true,
        //                         'message' => 'Successfully save.',
        //                     ], JsonResponse::HTTP_OK);
        //                 }
        //                 return new JsonResponse([
        //                     'success' => false,
        //                     'message' => 'Employee ' . $data->fullname_first . " doesn't have a position",
        //                 ], 400);
        //             } else {
        //                 return new JsonResponse([
        //                     'success' => false,
        //                     'message' => 'User ' . $data->fullname_first . " not found as not a current employee",
        //                 ], 400);
        //             }
        //         }
        //     }
        // } catch (\Throwable $th) {
        //     return new JsonResponse([
        //         'success' => false,
        //         'error' => $th->getMessage(),
        //         'message' => 'Failed save.',
        //     ], 400);
        // }
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
            'data' => $myRequest
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
            'data' => $myApproval
        ]);
    }
}

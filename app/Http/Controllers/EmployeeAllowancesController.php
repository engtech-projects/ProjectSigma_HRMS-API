<?php

namespace App\Http\Controllers;

use App\Enums\AssignTypes;
use App\Http\Requests\FilterEmployeeAllowancesRequest;
use App\Models\EmployeeAllowances;
use App\Http\Requests\StoreEmployeeAllowancesRequest;
use App\Http\Requests\UpdateEmployeeAllowancesRequest;
use App\Models\Employee;
use App\Models\InternalWorkExperience;
use Illuminate\Http\JsonResponse;

class EmployeeAllowancesController extends Controller
{
    public const DEPARTMENT = "App\Models\Department";
    public const PROJECT = "App\Models\Project";

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
                    'message' => 'Successfully save.',
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
                foreach ($request["employees"] as $key) {
                    $data = Employee::with('current_employment.position.allowances')->find($key);
                    if ($data->current_employment) {
                        if ($data->current_employment->position_id) {

                            $data_amt = $data->current_employment->position->allowances->amount;
                            if ($data_amt == null) {
                                return new JsonResponse([
                                    'success' => false,
                                    'message' => 'Allowance amount not found',
                                ], 400);
                            }
                            $employee_allowance = new EmployeeAllowances();
                            $type = $request["group_type"];
                            switch ($type) {
                                case AssignTypes::DEPARTMENT->value:
                                    $employee_allowance->charge_assignment_type = EmployeeAllowancesController::DEPARTMENT;
                                    $employee_allowance->charge_assignment_id = $request["department_id"];
                                    break;
                                case AssignTypes::PROJECT->value:
                                    $employee_allowance->charge_assignment_type = EmployeeAllowancesController::PROJECT;
                                    $employee_allowance->charge_assignment_id = $request["project_id"];
                                    break;
                            }
                            $employee_allowance->allowance_date = $request["allowance_date"];
                            $employee_allowance->cutoff_start = $request["cutoff_start"];
                            $employee_allowance->cutoff_end = $request["cutoff_end"];
                            $employee_allowance->total_days = $request["total_days"];
                            $employee_allowance->allowance_amount = $data_amt;
                            $employee_allowance->save();

                            return new JsonResponse([
                                'success' => true,
                                'message' => 'Successfully save.',
                            ], JsonResponse::HTTP_OK);
                        }
                        return new JsonResponse([
                            'success' => false,
                            'message' => 'Employee ' . $data->fullname_first . " doesn't have a position",
                        ], 400);
                    } else {
                        return new JsonResponse([
                            'success' => false,
                            'message' => 'User ' . $data->fullname_first . " not found as not a current employee",
                        ], 400);
                    }
                }
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
    public function show(EmployeeAllowances $employeeAllowances)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmployeeAllowances $employeeAllowances)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeAllowancesRequest $request, EmployeeAllowances $employeeAllowances)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmployeeAllowances $employeeAllowances)
    {
        //
    }
}

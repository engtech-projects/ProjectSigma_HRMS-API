<?php

namespace App\Http\Controllers;

use App\Enums\AssignTypes;
use App\Models\EmployeeAllowances;
use App\Http\Requests\StoreEmployeeAllowancesRequest;
use App\Http\Requests\UpdateEmployeeAllowancesRequest;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;

class EmployeeAllowancesController extends Controller
{
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
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeAllowancesRequest $request)
    {
        $valData = $request->validated();
        try {
            if ($valData) {
                $data = Employee::with('current_employment.position.allowances')->find($request["employee_id"]);
                $data_amt = $data->current_employment->position->allowances->amount;
                $employee_allowance = new EmployeeAllowances();
                $type = $request["group_type"];
                $employee_allowance->charge_assignment_type = $type;
                switch ($type) {
                    case AssignTypes::DEPARTMENT->value:
                        $employee_allowance->charge_assignment_id = $request["department_id"];
                        break;
                    case AssignTypes::PROJECT->value:
                        $employee_allowance->charge_assignment_id = $request["project_id"];
                        break;
                }
                $employee_allowance->allowance_date = $request["allowance_date"];
                $employee_allowance->allowance_amount = $data_amt;
                $employee_allowance->save();
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

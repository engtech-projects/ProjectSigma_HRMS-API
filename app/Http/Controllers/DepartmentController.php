<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Models\Employee;
use App\Models\InternalWorkExperience;
use Illuminate\Http\JsonResponse;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $department = Department::paginate(15);
        $data = json_decode('{}');
        $data->message = "Successfully fetch.";
        $data->success = true;
        $data->data = $department;
        return response()->json($data);
    }

    public function get()
    {
        $employeeList = Employee::whereHas('employee_internal', function ($query) {
            $query->statusCurrent();
        })->with(['employee_internal' => function ($query) {
            $query->withOut(['employee_salarygrade']);
        }, 'employee_has_projects'])->get();

        $employeeCollection = collect($employeeList)->map(function ($employee) {
            $department = $employee->employee_internal->first()->employee_department;
            return [
                "id" => $employee->id,
                "first_name" => $employee->first_name,
                "middle_name" => $employee->middle_name,
                "family_name" => $employee->family_name,
                "name_suffix" => $employee->name_suffix,
                "nick_name" => $employee->nick_name,
                "gender" => $employee->gender,
                "department" => $department,
                "project" => $employee->employee_has_projects
            ];
        });

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully fetched.',
            'data' => $employeeCollection,
        ]);
        /* $main = Department::get();
        $data = json_decode('{}');
        $data->message = "Successfully fetch.";
        $data->success = true;
        $data->data = $main;
        return response()->json($data); */
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDepartmentRequest $request)
    {
        $department = new Department();
        $department->fill($request->validated());
        $data = json_decode('{}');
        if (!$department->save()) {
            $data->message = "Save failed.";
            $data->success = false;
            return response()->json($data, 400);
        }
        $data->message = "Successfully save.";
        $data->success = true;
        $data->data = $department;
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $department = Department::find($id);
        $data = json_decode('{}');
        if (!is_null($department)) {
            $data->message = "Successfully fetch.";
            $data->success = true;
            $data->data = $department;
            return response()->json($data);
        }
        $data->message = "No data found.";
        $data->success = false;
        return response()->json($data, 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDepartmentRequest $request, $id)
    {
        $department = Department::find($id);
        $data = json_decode('{}');
        if (!is_null($department)) {
            $department->fill($request->validated());
            if ($department->save()) {
                $data->message = "Successfully update.";
                $data->success = true;
                $data->data = $department;
                return response()->json($data);
            }
            $data->message = "Failed update.";
            $data->success = false;
            return response()->json($data, 400);
        }
        $data->message = "Failed update.";
        $data->success = false;
        return response()->json($data, 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $department = Department::find($id);
        $data = json_decode('{}');
        if (!is_null($department)) {
            if ($department->delete()) {
                $data->message = "Successfully delete.";
                $data->success = true;
                $data->data = $department;
                return response()->json($data);
            }
            $data->message = "Failed delete.";
            $data->success = false;
            return response()->json($data, 400);
        }
        $data->message = "Failed delete.";
        $data->success = false;
        return response()->json($data, 404);
    }
}

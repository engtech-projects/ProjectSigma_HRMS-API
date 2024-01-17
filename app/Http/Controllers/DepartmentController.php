<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $department = Department::paginate(15);
        $data = json_decode('{}'); 
        $data->message = "Successfully Fetch";
        $data->success = true;
        $data->data = $department;
        return response()->json($data);
        // dd($department);     
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
    public function store(StoreDepartmentRequest $request)
    {
        $department = new Department;
        $department->fill($request->validated());
        $data = json_decode('{}'); 
        if(!$department->save()){
            $data->message = "Save unsuccessfull";
            $data->success = false;
            return response()->json($data, 400);
        }
        $data->message = "Successfully Save";
        $data->success = true;
        $data->data = $department;
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        $data = json_decode('{}'); 
        $data->message = "Successfully Fetch";
        $data->success = true;
        $data->data = $department;
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department)
    {
        //

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDepartmentRequest $request, $id)
    {
        $department = Department::find($id);
        $department->fill($request->validated());
        $data = json_decode('{}'); 
        if($department->save()){
            $data->message = "Successfully Update";
            $data->success = true;
            $data->data = $department;
            return response()->json($data);
        }
        $data->message = "Update unsuccessfull";
        $data->success = false;
        return response()->json($data, 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        $data = json_decode('{}'); 
        if($department->delete()){
            $data->message = "Successfully Deleted";
            $data->success = true;
            $data->data = $department;
            return response()->json($data);
        }
        $data->message = "Deleted unsuccessfull";
        $data->success = false;
        return response()->json($data,400); 
    }
}

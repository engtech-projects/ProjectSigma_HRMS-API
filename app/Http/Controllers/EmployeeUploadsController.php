<?php

namespace App\Http\Controllers;

use App\Models\EmployeeUploads;
use App\Http\Requests\StoreEmployeeUploadsRequest;
use App\Http\Requests\UpdateEmployeeUploadsRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class EmployeeUploadsController extends Controller
{
    const EMPLOYEEDIR = "employee_folder/";

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $main = EmployeeUploads::paginate(15);
        $data = json_decode('{}');
        $data->message = "Successfully fetch.";
        $data->success = true;
        $data->data = $main;
        return response()->json($data);
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
    public function store(StoreEmployeeUploadsRequest $request)
    {
        $main = new EmployeeUploads;
        $main->fill($request->validated());
        $data = json_decode('{}');

        $file_location = $request->file('file');

        $hashmake = Hash::make('secret');
        $hashname = hash('sha256',$hashmake);

        $name = $file_location->getClientOriginalName();

        $file_location->storePubliclyAs(EmployeeUploadsController::EMPLOYEEDIR.$hashname, $name,'public');

        $main->file_location = EmployeeUploadsController::EMPLOYEEDIR.$hashname."/".$name;

        if(!$main->save()){
            $data->message = "Save failed.";
            $data->success = false;
            return response()->json($data, 400);
        }

        $data->message = "Successfully save.";
        $data->success = true;
        $data->data = $main;
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $main = EmployeeUploads::find($id);
        $data = json_decode('{}');
        if (!is_null($main) ) {
            $data->message = "Successfully fetch.";
            $data->success = true;
            $data->data = $main;
            return response()->json($data);
        }
        $data->message = "No data found.";
        $data->success = false;
        return response()->json($data, 404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmployeeUploads $employeeUploads)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeUploadsRequest $request,  $id)
    {
        $main = EmployeeUploads::find($id);
        $data = json_decode('{}');
        if (!is_null($main) ) {
            $a = explode("/", $main->file_location);
            $main->fill($request->validated());
            $hashmake = Hash::make('secret');
            $hashname = hash('sha256',$hashmake);
            if($request->hasFile("file")){
                $file = $request->file('resume_attachment');
                $name = $file->getClientOriginalName();
                $file->storePubliclyAs(EmployeeUploadsController::EMPLOYEEDIR.$hashname, $name,'public');
                Storage::deleteDirectory("public/".$a[0]."/".$a[1]);
                $main->file_location = EmployeeUploadsController::EMPLOYEEDIR.$hashname."/".$name;
            }

            if($main->save()){
                $data->message = "Successfully update.";
                $data->success = true;
                $data->data = $main;
                return response()->json($data);
            }
            $data->message = "Update failed.";
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
        //
        $main = EmployeeUploads::find($id);
        $data = json_decode('{}');
        if (!is_null($main) ) {
            $a = explode("/", $main->file_location);
            if($main->delete()){
                Storage::deleteDirectory("public/".EmployeeUploadsController::EMPLOYEEDIR."/".$a[0]."/".$a[1]);
                $data->message = "Successfully delete.";
                $data->success = true;
                $data->data = $main;
                return response()->json($data);
            }
            $data->message = "Failed delete.";
            $data->success = false;
            return response()->json($data,400);
        }
        $data->message = "Failed delete.";
        $data->success = false;
        return response()->json($data,404);
    }
}

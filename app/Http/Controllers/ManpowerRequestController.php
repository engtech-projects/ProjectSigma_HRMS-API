<?php

namespace App\Http\Controllers;

use App\Models\ManpowerRequest;
use App\Http\Requests\StoreManpowerRequestRequest;
use App\Http\Requests\UpdateManpowerRequestRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Hash;

class ManpowerRequestController extends Controller
{
    const JDDIR = "job_description/";
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $main = ManpowerRequest::simplePaginate(15);
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
    public function store(StoreManpowerRequestRequest $request)
    {
        $main = new ManpowerRequest;
        $main->fill($request->validated());
        $data = json_decode('{}');
        $main->approvals = json_encode($request->approvals);
        $file = $request->file('job_description_attachment');
        $hashmake = Hash::make('secret');
        $hashname = hash('sha256',$hashmake);
        $name = $file->getClientOriginalName();
        $path = $file->storePubliclyAs(ManpowerRequestController::JDDIR.$hashname, $name,'public');
        $main->job_description_attachment = ManpowerRequestController::JDDIR.$hashname."/".$name;
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
        $main = ManpowerRequest::find($id);
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
    public function edit(ManpowerRequest $manpowerRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateManpowerRequestRequest $request, $id)
    {
        $main = ManpowerRequest::find($id);
        $data = json_decode('{}');
        if (!is_null($main) ) {
            $a = explode("/", $main->job_description_attachment);
            $main->fill($request->validated());
            if($request->hasFile("job_description_attachment")){
                $check = ManpowerRequest::find($id);
                $file = $request->file('job_description_attachment');
                $hashmake = Hash::make('secret');
                $hashname = hash('sha256',$hashmake);
                $name = $file->getClientOriginalName();
                $path = $file->storePubliclyAs(ManpowerRequestController::JDDIR.$hashname, $name,'public');
                Storage::deleteDirectory("public/".$a[0]."/".$a[1]);
                $main->job_description_attachment = ManpowerRequestController::JDDIR.$hashname."/".$name;
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
        $main = ManpowerRequest::find($id);
        $a = explode("/", $main->job_description_attachment);
        Storage::deleteDirectory("public/".$a[0]."/".$a[1]);
        $data = json_decode('{}');
        if (!is_null($main) ) {
            if($main->delete()){
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

<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Http\Requests\StoreLeaveRequest;
use App\Http\Requests\UpdateLeaveRequest;

class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $leave = Leave::paginate(15);
        $data = json_decode('{}'); 
        $data->message = "successfully fetch all";
        $data->success = true;
        $data->data = $leave;
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
    public function store(StoreLeaveRequest $request)
    {
        $leave = new Leave;
        $leave->fill($request->validated());
        $leave->employment_type = json_encode($request->employment_type);
        $data = json_decode('{}'); 
        if(!$leave->save()){
            $data->message = "failed to store data";
            $data->success = false;
            return response()->json($data, 400);
        }
        $data->message = "successfully store data";
        $data->success = true;
        $data->data = $leave;
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $leave = Leave::find($id);
        $data = json_decode('{}'); 
        $data->message = "successfully show data";
        $data->success = true;
        $data->data = $leave;
        if($data->data==null){
            $data->message = "no data found";
            $data->success = false;
        }
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Leave $leave)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLeaveRequest $request,  $id)
    {
        $leave = Leave::find($id);
        $leave->fill($request->validated());
        $leave->employment_type = json_encode($request->employment_type);
        $data = json_decode('{}'); 
        if($leave->save()){
            $data->message = "successfully update data";
            $data->success = true;
            $data->data = $leave;
            return response()->json($data);
        }
        $data->message = "failed update data";
        $data->success = false;
        return response()->json($data, 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $leave = Leave::find($id);
        $data = json_decode('{}'); 
        if($leave->delete()){
            $data->message = "successfully update data";
            $data->success = true;
            $data->data = $leave;
            return response()->json($data);
        }
        $data->message = "failed delete data";
        $data->success = false;
        return response()->json($data,400); 
    }
}

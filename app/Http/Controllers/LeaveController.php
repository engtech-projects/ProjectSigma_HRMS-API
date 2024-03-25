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
        $leave = Leave::paginate(15);
        $data = json_decode('{}');
        $data->message = "Successfully fetch.";
        $data->success = true;
        $data->data = $leave;
        return response()->json($data);
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
        if (!$leave->save()) {
            $data->message = "Save failed.";
            $data->success = false;
            return response()->json($data, 400);
        }
        $data->message = "Successfully save.";
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
        if (!is_null($leave)) {
            $data->message = "Successfully fetch.";
            $data->success = true;
            $data->data = $leave;
            return response()->json($data);
        }
        $data->message = "No data found.";
        $data->success = false;
        return response()->json($data, 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLeaveRequest $request,  $id)
    {
        $leave = Leave::find($id);
        $data = json_decode('{}');
        if (!is_null($leave)) {
            $leave->fill($request->validated());
            $leave->employment_type = json_encode($request->employment_type);
            if ($leave->save()) {
                $data->message = "Successfully update.";
                $data->success = true;
                $data->data = $leave;
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
        $leave = Leave::find($id);
        $data = json_decode('{}');
        if (!is_null($leave)) {
            if ($leave->delete()) {
                $data->message = "Successfully delete.";
                $data->success = true;
                $data->data = $leave;
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

<?php

namespace App\Http\Controllers;

use App\Models\allowance;
use App\Http\Requests\StoreallowanceRequest;
use App\Http\Requests\UpdateallowanceRequest;

class AllowanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $main = allowance::simplePaginate(15); 
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
    public function store(StoreallowanceRequest $request)
    {
        //
        $main = new allowance;
        $main->fill($request->validated());
        $data = json_decode('{}'); 
        
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
        $main = allowance::find($id);
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
    public function edit(allowance $allowance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateallowanceRequest $request, $id)
    {
        //
        $main = allowance::find($id);
        $data = json_decode('{}');
        if (!is_null($main) ) {
            $main->fill($request->validated());
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
        $main = allowance::find($id);
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

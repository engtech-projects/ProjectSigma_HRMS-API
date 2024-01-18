<?php

namespace App\Http\Controllers;

use App\Models\settings;
use App\Http\Requests\StoresettingsRequest;
use App\Http\Requests\UpdatesettingsRequest;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = settings::simplePaginate(15); 
        $data = json_decode('{}'); 
        $data->message = "Successfully fetch.";
        $data->success = true;
        $data->data = $settings;     
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
    public function store(StoresettingsRequest $request)
    {
        //
        $settings = new settings;
        $settings->fill($request->validated());
        $data = json_decode('{}'); 
        
        if(!$settings->save()){
            $data->message = "Save failed.";
            $data->success = false;
            return response()->json($data, 400);
        }
        $data->message = "Successfully save.";
        $data->success = true;
        $data->data = $settings;
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $settings = settings::find($id);
        $data = json_decode('{}');
        if (!is_null($settings) ) {
            $data->message = "Successfully fetch.";
            $data->success = true;
            $data->data = $settings;
            return response()->json($data);
        }
        $data->message = "No data found.";
        $data->success = false;
        return response()->json($data, 404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(settings $settings)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatesettingsRequest $request, $id)
    {
        $settings = settings::find($id);
        $data = json_decode('{}');
        if (!is_null($settings) ) {
            $settings->fill($request->validated());
            if($settings->save()){
                $data->message = "Successfully update.";
                $data->success = true;
                $data->data = $settings;
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
        $settings = settings::find($id);
        $data = json_decode('{}');
        if (!is_null($settings) ) {
            if($settings->delete()){
                $data->message = "Successfully delete.";
                $data->success = true;
                $data->data = $settings;
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

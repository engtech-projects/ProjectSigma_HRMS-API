<?php

namespace App\Http\Controllers;

use App\Models\SSSContribution;
use App\Http\Requests\StoreSSSContributionRequest;
use App\Http\Requests\UpdateSSSContributionRequest;

class SSSContributionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sss = SSSContribution::paginate(15);
        $data = json_decode('{}'); 
        $data->message = "Successfully fetch.";
        $data->success = true;
        $data->data = $sss;
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
    public function store(StoreSSSContributionRequest $request)
    {
        $sss = new SSSContribution;
        $sss->fill($request->validated());
        $data = json_decode('{}'); 
        if(!$sss->save()){
            $data->message = "Save unsuccessfull.";
            $data->success = false;
            return response()->json($data, 400);
        }
        $data->message = "Successfully save.";
        $data->success = true;
        $data->data = $sss;
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $sss = SSSContribution::find($id);
        $data = json_decode('{}'); 
        $data->message = "Successfully fetch.";
        $data->success = true;
        $data->data = $sss;
        if($data->data==null){
            $data->message = "No data found.";
            $data->success = false;
        }
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SSSContribution $sSSContribution)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSSSContributionRequest $request, $id)
    {
        $sss = SSSContribution::find($id);
        $sss->fill($request->validated());
        $data = json_decode('{}'); 
        if($sss->save()){
            $data->message = "Successfully update.";
            $data->success = true;
            $data->data = $sss;
            return response()->json($data);
        }
        $data->message = "Update unsuccessfull.";
        $data->success = false;
        return response()->json($data, 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $sss = SSSContribution::find($id);
        $data = json_decode('{}'); 
        if($sss->delete()){
            $data->message = "Successfully deleted.";
            $data->success = true;
            $data->data = $sss;
            return response()->json($data);
        }
        $data->message = "Delete unsuccessfull.";
        $data->success = false;
        return response()->json($data,400); 
    }
}

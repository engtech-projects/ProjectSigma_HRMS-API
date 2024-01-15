<?php

namespace App\Http\Controllers;

use App\Models\WitholdingTaxContribution;
use App\Http\Requests\StoreWitholdingTaxContributionRequest;
use App\Http\Requests\UpdateWitholdingTaxContributionRequest;

class WitholdingTaxContributionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $witholdingtax = WitholdingTaxContribution::paginate(15);
        $data = json_decode('{}'); 
        $data->message = "successfully fetch all";
        $data->success = true;
        $data->data = $witholdingtax;
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
    public function store(StoreWitholdingTaxContributionRequest $request)
    {
        $witholdingtax = new WitholdingTaxContribution;
        $witholdingtax->fill($request->validated());
        $data = json_decode('{}'); 
        if(!$witholdingtax->save()){
            $data->message = "failed to store data";
            $data->success = false;
            return response()->json($data, 400);
        }
        $data->message = "successfully store data";
        $data->success = true;
        $data->data = $witholdingtax;
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $witholdingtax = WitholdingTaxContribution::find($id);
        $data = json_decode('{}'); 
        $data->message = "successfully show data";
        $data->success = true;
        $data->data = $witholdingtax;
        if($data->data==null){
            $data->message = "no data found";
            $data->success = false;
        }
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WitholdingTaxContribution $witholdingTaxContribution)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWitholdingTaxContributionRequest $request, $id)
    {
        $witholdingtax = WitholdingTaxContribution::find($id);
        $witholdingtax->fill($request->validated());
        $data = json_decode('{}'); 
        if($witholdingtax->save()){
            $data->message = "successfully update data";
            $data->success = true;
            $data->data = $witholdingtax;
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
        $witholdingtax = WitholdingTaxContribution::find($id);
        $data = json_decode('{}'); 
        if($witholdingtax->delete()){
            $data->message = "successfully update data";
            $data->success = true;
            $data->data = $witholdingtax;
            return response()->json($data);
        }
        $data->message = "failed delete data";
        $data->success = false;
        return response()->json($data,400); 
    }
}

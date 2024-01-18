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
        $data->message = "Successfully fetch.";
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
            $data->message = "Save failed.";
            $data->success = false;
            return response()->json($data, 400);
        }
        $data->message = "Successfully save.";
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
        if (!is_null($witholdingtax) ) {
            $data->message = "Successfully fetch.";
            $data->success = true;
            $data->data = $witholdingtax;
            return response()->json($data);
        }
        $data->message = "No data found.";
        $data->success = false;
        return response()->json($data, 404);
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
        if (!is_null($witholdingtax) ) {
            if($witholdingtax->save()){
                $data->message = "Successfully update.";
                $data->success = true;
                $data->data = $witholdingtax;
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
        $witholdingtax = WitholdingTaxContribution::find($id);
        $data = json_decode('{}'); 
        if (!is_null($witholdingtax) ) {
            if($witholdingtax->delete()){
                $data->message = "Successfully delete.";
                $data->success = true;
                $data->data = $witholdingtax;
                return response()->json($data);
            }
            $data->message = "Failed delete.";
            $data->success = false;
            return response()->json($data,400); 
        }
        $data->message = "Failed delete.";
        $data->success = false;
        return response()->json($data, 404);
    }
}

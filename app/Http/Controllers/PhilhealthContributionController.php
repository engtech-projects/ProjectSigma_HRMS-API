<?php

namespace App\Http\Controllers;

use App\Models\PhilhealthContribution;
use App\Http\Requests\StorePhilhealthContributionRequest;
use App\Http\Requests\UpdatePhilhealthContributionRequest;

class PhilhealthContributionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sss = PhilhealthContribution::paginate(15);
        $data = json_decode('{}'); 
        $data->message = "successfully fetch all";
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
    public function store(StorePhilhealthContributionRequest $request)
    {
        $philhealth = new PhilhealthContribution;
        $philhealth->fill($request->validated());
        $data = json_decode('{}'); 
        if(!$philhealth->save()){
            $data->message = "failed to store data";
            $data->success = false;
            return response()->json($data, 400);
        }
        $data->message = "successfully store data";
        $data->success = true;
        $data->data = $philhealth;
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $philhealth = PhilhealthContribution::find($id);
        $data = json_decode('{}'); 
        $data->message = "successfully show data";
        $data->success = true;
        $data->data = $philhealth;
        if($data->data==null){
            $data->message = "no data found";
            $data->success = false;
        }
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PhilhealthContribution $philhealthContribution)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePhilhealthContributionRequest $request, $id)
    {
        $philhealth = PhilhealthContribution::find($id);
        $philhealth->fill($request->validated());
        $data = json_decode('{}'); 
        if($philhealth->save()){
            $data->message = "successfully update data";
            $data->success = true;
            $data->data = $philhealth;
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
        $philhealth = PhilhealthContribution::find($id);
        $data = json_decode('{}'); 
        if($philhealth->delete()){
            $data->message = "successfully update data";
            $data->success = true;
            $data->data = $philhealth;
            return response()->json($data);
        }
        $data->message = "failed delete data";
        $data->success = false;
        return response()->json($data,400); 
    }
}

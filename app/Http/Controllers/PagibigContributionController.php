<?php

namespace App\Http\Controllers;

use App\Models\PagibigContribution;
use App\Http\Requests\StorePagibigContributionRequest;
use App\Http\Requests\UpdatePagibigContributionRequest;

class PagibigContributionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $pagibig = PagibigContribution::paginate(15);
        $data = json_decode('{}'); 
        $data->message = "successfully fetch all";
        $data->success = true;
        $data->data = $pagibig;
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
    public function store(StorePagibigContributionRequest $request)
    {
        //
        $pagibig = new PagibigContribution;
        $pagibig->fill($request->validated());
        $data = json_decode('{}'); 
        if(!$pagibig->save()){
            $data->message = "failed to store data";
            $data->success = false;
            return response()->json($data, 400);
        }
        $data->message = "successfully store data";
        $data->success = true;
        $data->data = $pagibig;
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $pagibig = PagibigContribution::find($id);
        $data = json_decode('{}'); 
        $data->message = "successfully show data";
        $data->success = true;
        $data->data = $pagibig;
        if($data->data==null){
            $data->message = "no data found";
            $data->success = false;
        }
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PagibigContribution $pagibigContribution)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePagibigContributionRequest $request, $id)
    {
        //
        $pagibig = PagibigContribution::find($id);
        $pagibig->fill($request->validated());
        $data = json_decode('{}'); 
        if($pagibig->save()){
            $data->message = "successfully update data";
            $data->success = true;
            $data->data = $pagibig;
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
        //
        $pagibig = PagibigContribution::find($id);
        $data = json_decode('{}'); 
        if($pagibig->delete()){
            $data->message = "successfully update data";
            $data->success = true;
            $data->data = $pagibig;
            return response()->json($data);
        }
        $data->message = "failed delete data";
        $data->success = false;
        return response()->json($data,400); 
    }
}

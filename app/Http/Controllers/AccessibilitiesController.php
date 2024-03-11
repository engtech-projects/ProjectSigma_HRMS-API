<?php

namespace App\Http\Controllers;

use App\Models\Accessibilities;
use App\Http\Requests\StoreAccessibilitiesRequest;
use App\Http\Requests\UpdateAccessibilitiesRequest;

class AccessibilitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $access = Accessibilities::simplePaginate(15);
        return response()->json($access);
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
    public function store(StoreAccessibilitiesRequest $request)
    {
        $access = new Accessibilities;
        $access->fill($request->validated());
        if(!$access->save()){
            return response()->json(["msg"=>"error"], 400);
        }
        return response()->json($access);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $accessibilities = Accessibilities::find($id);
        return response()->json($accessibilities);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Accessibilities $accessibilities)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAccessibilitiesRequest $request, $id)
    {
        $accessibilities = Accessibilities::find($id);
        $accessibilities->fill($request->validated());
        if($accessibilities->save()){
            return response()->json($accessibilities);
        }
        return response()->json(["msg"=>"error"], 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $accessibilities = Accessibilities::find($id);
        if($accessibilities->delete()){
            return response()->json($accessibilities);
        }
        return response()->json(["msg"=>"error"],400);
    }
}

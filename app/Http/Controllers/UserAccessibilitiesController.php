<?php

namespace App\Http\Controllers;

use App\Models\UserAccessibilities;
use App\Http\Requests\StoreUserAccessibilitiesRequest;
use App\Http\Requests\UpdateUserAccessibilitiesRequest;

class UserAccessibilitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user_accessibilities = UserAccessibilities::simplePaginate(15);      
        return response()->json($user_accessibilities);
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
    public function store(StoreUserAccessibilitiesRequest $request)
    {
        // dd($request);
        $user_accessibilities = new UserAccessibilities;
        $user_accessibilities->fill($request->validated());
        $user_accessibilities->options = json_encode($request->options);
        if(!$user_accessibilities->save()){
            return response()->json(["msg"=>"error"], 400);
        }
        return response()->json($user_accessibilities);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $user_accessibilities = UserAccessibilities::find($id);
        return response()->json($user_accessibilities);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserAccessibilities $userAccessibilities)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserAccessibilitiesRequest $request, $id)
    {
        // dd($user_accessibilities);
        $user_accessibilities = UserAccessibilities::find($id);
        $user_accessibilities->fill($request->validated());
        $user_accessibilities->options = json_encode($request->options);
        if($user_accessibilities->save()){
            return response()->json($user_accessibilities);
        }
        return response()->json(["msg"=>"error"], 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user_accessibilities = UserAccessibilities::find($id);
        if($user_accessibilities->delete()){
            return response()->json($user_accessibilities);
        }
        return response()->json(["msg"=>"error"],400); 
    }
}

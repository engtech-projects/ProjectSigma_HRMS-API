<?php

namespace App\Http\Controllers;

use App\Models\Users;
use App\Models\User;
use App\Http\Requests\StoreUsersRequest;
use App\Http\Requests\UpdateUsersRequest;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = Users::simplePaginate(15);      
        return response()->json($users);
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
    public function store(StoreUsersRequest $request)
    {
        $users = new Users;
        $users->fill($request->validated());
        if(!$users->save()){
            return response()->json(["msg"=>"error"], 400);
        }
        return response()->json($users);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $users = Users::find($id);
        return response()->json($users);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Users $users)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUsersRequest $request, $id)
    {   
        $users = Users::find($id);
        $users->fill($request->validated());
        if($users->save()){
            return response()->json($users);
        }
        return response()->json(["msg"=>"error"], 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $users = Users::find($id);
        if($users->delete()){
            return response()->json($users);
        }
        return response()->json(["msg"=>"error"],400); 
    }
}

<?php

namespace App\Http\Controllers;

use App\Enums\UserTypes;
use App\Models\Users;
use App\Models\User;
use App\Http\Requests\StoreUsersRequest;
use App\Http\Requests\UpdateUsersRequest;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = Users::simplePaginate(15);
        $data = json_decode('{}');
        $data->message = "Successfully fetch.";
        $data->success = true;
        $data->data = $users;
        return response()->json($data);
    }

    /**
     * Display a listing of the resource.
     */
    public function get()
    {
        $users = Users::where('type', UserTypes::EMPLOYEE);
        $data = json_decode('{}');
        $data->message = "Successfully fetch.";
        $data->success = true;
        $data->data = $users;
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUsersRequest $request)
    {
        $users = new Users;
        $users->fill($request->validated());
        $users->password = Hash::make($request->password);
        $users->accessibilities = json_encode($request->accessibilities);
        $data = json_decode('{}');

        if(!$users->save()){
            $data->message = "Save failed.";
            $data->success = false;
            return response()->json($data, 400);
        }
        $data->message = "Successfully save.";
        $data->success = true;
        $data->data = $users;
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $users = Users::find($id);
        $data = json_decode('{}');
        if (!is_null($users) ) {
            $data->message = "Successfully fetch.";
            $data->success = true;
            $data->data = $users;
            return response()->json($data);
        }
        $data->message = "No data found.";
        $data->success = false;
        return response()->json($data, 404);
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
        $data = json_decode('{}');
        if (!is_null($users) ) {
            $users->fill($request->validated());
            $users->accessibilities = json_encode($request->accessibilities);
            if($users->save()){
                $data->message = "Successfully update.";
                $data->success = true;
                $data->data = $users;
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
        $users = Users::find($id);
        $data = json_decode('{}');
        if (!is_null($users) ) {
            if($users->delete()){
                $data->message = "Successfully delete.";
                $data->success = true;
                $data->data = $users;
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

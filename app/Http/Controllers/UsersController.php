<?php

namespace App\Http\Controllers;

use App\Enums\SetupSettingsEnums;
use App\Enums\UpdateTypesOnUser;
use App\Enums\UserTypes;
use App\Models\Users;
use App\Http\Requests\StoreUsersRequest;
use App\Http\Requests\UpdateUserCredentialRequest;
use App\Http\Requests\UpdateUsersRequest;
use App\Http\Resources\UserEmployeeResource;
use App\Models\Settings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = Users::paginate(config("app.pagination_per_page"));
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
        $users = Users::where('type', UserTypes::EMPLOYEE)
        ->with("employee")
        ->get();
        $usersCollection = UserEmployeeResource::collection($users)
        ->sortBy("employee.fullname_first", SORT_NATURAL)
        ->values()
        ->all();
        $data = json_decode('{}');
        $data->message = "Successfully fetch.";
        $data->success = true;
        $data->data = $usersCollection;
        return response()->json($data);
    }
    public function getUserAccountByEmployeeId($id)
    {
        $users = Users::where('employee_id', $id)->with("employee")->first();
        $data = json_decode('{}');
        $data->message = "Successfully fetch.";
        $data->success = true;
        $data->data = $users;
        return response()->json($data);
    }
    public function updateUserCredential(UpdateUserCredentialRequest $request)
    {
        $data = json_decode('{}');
        $id = auth()->user()->id;
        $users = Users::find($id);

        if (!$users || !password_verify($request->current_password, $users->password)) {
            return response()->json(['message' => 'Invalid Password'], 401);
        }

        switch ($request->typechange) {
            case UpdateTypesOnUser::NAME->value:
                $users->name = $request->name;
                break;
            case UpdateTypesOnUser::EMAIL->value:
                $users->email = $request->email;
                break;
            case UpdateTypesOnUser::PASSWORD->value:
                $users->password = $request->password;
                break;
        }

        if ($users->save()) {
            $data->message = "Successfully update.";
            $data->success = true;
            $data->data = $users;
            // Logout Other Devices
            $logoutOnChangepassword = boolval(Settings::getSettingValue(SetupSettingsEnums::LOGOUT_CHANGE_PASSWORD));
            if ($logoutOnChangepassword) {
                $request->user()->tokens()->where('id', '!=', $request->user()->currentAccessToken()->id)->delete();
            }
            return response()->json($data);
        }

        $data->message = "Failed update.";
        $data->success = false;
        return response()->json($data, 400);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUsersRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData["type"] = UserTypes::EMPLOYEE;
        $validatedData["password"] = Hash::make($validatedData["password"]);
        $validatedData["email_verified_at"] = Carbon::now();
        $user = Users::create($validatedData);
        $data = json_decode('{}');

        if (!$user->save()) {
            $data->message = "Save failed.";
            $data->success = false;
            return response()->json($data, 400);
        }
        $data->message = "Successfully save.";
        $data->success = true;
        $data->data = $user;
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $users = Users::find($id);
        $data = json_decode('{}');
        if (!is_null($users)) {
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
    public function update(UpdateUsersRequest $request, Users $user)
    {
        $data = json_decode('{}');
        if (!is_null($user)) {
            $user->fill($request->validated());
            if ($user->save()) {
                $data->message = "Successfully update.";
                $data->success = true;
                $data->data = $user;
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
        if (!is_null($users)) {
            if ($users->delete()) {
                $data->message = "Successfully delete.";
                $data->success = true;
                $data->data = $users;
                return response()->json($data);
            }
            $data->message = "Failed delete.";
            $data->success = false;
            return response()->json($data, 400);
        }
        $data->message = "Failed delete.";
        $data->success = false;
        return response()->json($data, 404);
    }
}

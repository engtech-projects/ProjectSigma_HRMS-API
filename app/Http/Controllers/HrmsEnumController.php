<?php

namespace App\Http\Controllers;

use App\Enums\UserTypes;
use App\Http\Resources\UserEmployeeResource;
use App\Models\Users;
use Illuminate\Support\Facades\Log;

class HrmsEnumController extends Controller
{
    //
    public function employeeHeads()
    {
        $users = Users::where('type', UserTypes::EMPLOYEE)
        ->whereHas("employee.current_employment.position", function ($query) {
            $query->where("position_type", "head");
        })
        ->get();
        $usersCollection = UserEmployeeResource::collection($users)
        ->sortBy("employee.fullname_first", SORT_NATURAL)
        ->values()
        ->all();
        return response()->json(
            [
                'data' => $usersCollection,
                'success' => true,
                'message' => 'Successfully fetch.'
            ]
        );
    }

    public function approvalUsers()
    {
        $users = Users::where('type', UserTypes::EMPLOYEE)
        ->where("id", "!=", auth()->user()->id)
        ->whereHas("employee", function ($query) {
            $query->isActive();
        })
        ->get();
        $usersCollection = UserEmployeeResource::collection($users)
        ->sortBy("employee.fullname_last", SORT_NATURAL)
        ->values()
        ->all();
        return response()->json(
            [
                'data' => $usersCollection,
                'success' => true,
                'message' => 'Successfully fetch.'
            ]
        );
    }
    public function approvalHeads()
    {
        $users = Users::where('type', UserTypes::EMPLOYEE)
        ->whereHas("employee", function ($query) {
            $query->isActive();
        })
        ->whereHas("employee.current_employment.position", function ($query) {
            $query->where("position_type", "head");
        })
        ->where("id", "!=", auth()->user()->id)
        ->get();
        $usersCollection = UserEmployeeResource::collection($users)
        ->sortBy("employee.fullname_last", SORT_NATURAL)
        ->values()
        ->all();
        return response()->json(
            [
                'data' => $usersCollection,
                'success' => true,
                'message' => 'Successfully fetch.'
            ]
        );
    }
}

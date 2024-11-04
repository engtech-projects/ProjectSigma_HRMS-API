<?php

namespace App\Http\Controllers;

use App\Enums\UserTypes;
use App\Http\Resources\UserEmployeeResource;
use App\Models\Users;

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
}

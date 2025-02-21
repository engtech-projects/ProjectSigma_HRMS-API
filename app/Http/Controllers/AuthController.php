<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthUserRequest;
use App\Http\Resources\UserEmployeeCphotoResource;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use App\Models\EmployeePanRequest;
use App\Enums\UserTypes;
use App\Enums\EmployeeCompanyEmploymentsStatus;

class AuthController extends Controller
{
    public function login(AuthUserRequest $request)
    {
        $creds = $request->validated();

        $check_user = Users::where(
            [ 'name' => $creds['username']]
        )->first();

        if (!$check_user || !password_verify($creds["password"], $check_user->password)) {
            return response()->json([ 'message' => 'Invalid login details'  ], 401);
        }

        if ($check_user->type === UserTypes::EMPLOYEE->value && $check_user->employee->company_employments->status === EmployeeCompanyEmploymentsStatus::INACTIVE->value) {
            $panTermination = EmployeePanRequest::where([
                ["type", "=", "Termination"],
                ["employee_id", "=", $check_user->employee->id],
                ["request_status", "=", "Approved"],
            ])->latest('created_at')->first();

            if ($panTermination) {
                $dateTerminateApproved = $panTermination->updated_at ?? $panTermination->created_at;
                $daysDifference = Carbon::now()->diffInDays(Carbon::parse($dateTerminateApproved));

                if ($daysDifference > 30) {
                    return new JsonResponse([
                        "success" => false,
                        "message" => "Failed to log in. Employee Terminated",
                    ], JsonResponse::HTTP_UNAUTHORIZED);
                }
            }
        }

        $token = $check_user->createToken('auth_token:' . $check_user->id)->plainTextToken;
        return response()->json([
            'mesage' => 'Sign in successful.',
            'user_data' => $check_user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request)
    {

        if ($request->user()) {
            // $request->user()->tokens()->delete();
            $request->user()->currentAccessToken()->delete();
        }
        return response()->json(['message' => 'Logout'], 200);
    }

    public function session(Request $request)
    {
        if ($request->user()) {
            return response()->json(new UserEmployeeCphotoResource(Auth::user()), 200);
        }
        return  response()->json('Unauthenticated', 401);
    }

}

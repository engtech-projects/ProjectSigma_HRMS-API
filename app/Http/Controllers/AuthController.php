<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\Users;
use App\Models\Personnel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

            return response()->json(Auth::user(), 200);
        }
        return  response()->json('Unauthenticated', 401);
    }

}

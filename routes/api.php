<?php

use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\softDeletes;
use App\Http\Controllers\AccessibilitiesController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\UserAccessibilitiesController;
use App\Http\Controllers\SSSContributionController;
use App\Http\Controllers\UsersController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function(){
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/session', [AuthController::class, 'session']);
});

Route::resource('sss', SSSContributionController::class);
Route::resource('departments', DepartmentController::class);
Route::resource('user_accessibilities', UserAccessibilitiesController::class);
Route::resource('accessibilities', AccessibilitiesController::class);
Route::resource('users', UsersController::class);
       


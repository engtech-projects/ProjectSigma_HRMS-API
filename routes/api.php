<?php

use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccessibilitiesController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\SSSContributionController;
use App\Http\Controllers\PhilhealthContributionController;
use App\Http\Controllers\WitholdingTaxContributionController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\PagibigContributionController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\AllowanceController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\AnnouncementsController;
use App\Http\Controllers\ApprovalsController;
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
    // AUTH
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/session', [AuthController::class, 'session']);
    Route::get('user-list', [UsersController::class, 'get']);
});

Route::resource('sss', SSSContributionController::class);
Route::resource('philhealth', PhilhealthContributionController::class);
Route::resource('witholdingtax', WitholdingTaxContributionController::class);
Route::resource('leave', LeaveController::class);
Route::resource('pagibig', PagibigContributionController::class);
Route::resource('departments', DepartmentController::class);
Route::resource('accessibilities', AccessibilitiesController::class);
Route::resource('settings', SettingsController::class);
Route::resource('position', PositionController::class);
Route::resource('allowance', AllowanceController::class);
Route::resource('users', UsersController::class);
Route::resource('events', EventsController::class);
Route::resource('announcement', AnnouncementsController::class);
Route::resource('approvals', ApprovalsController::class);
Route::get('position-list', [PositionController::class, 'get']);
Route::get('allowance-list', [AllowanceController::class, 'get']);
Route::get('announcement-list', [AnnouncementsController::class, 'get']);


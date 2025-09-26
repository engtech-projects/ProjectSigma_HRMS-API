<?php

use App\Http\Controllers\PayslipController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return response()->json(['version' => app()->version()]);
});
Route::get('/login', function () {
    abort(401);
})->name('login');
// Route::get('/', function () {
//     $data = JobApplicants::findOrFail(1);
//     return view('reports.docs.application_form', ["application" => $data]);
// });
Route::get('payslip', [PayslipController::class, 'index']);

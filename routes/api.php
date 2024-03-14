<?php

use App\Http\Controllers\EmployeeBulkUploadController;
use App\Http\Controllers\SalaryGradeLevelController;
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
use App\Http\Controllers\ManpowerRequestController;
use App\Http\Controllers\JobApplicantsController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\CompanyEmployeeController;
use App\Http\Controllers\EmployeeUploadsController;
use App\Http\Controllers\EmployeeRecordController;
use App\Http\Controllers\EmployeeAddressController;
use App\Http\Controllers\EmployeeAffiliationController;
use App\Http\Controllers\EmployeeEducationController;
use App\Http\Controllers\EmployeeEligibilityController;
use App\Http\Controllers\EmployeePersonnelActionNoticeRequestController;
use App\Http\Controllers\EmployeeRelatedpersonController;
use App\Http\Controllers\EmployeeSeminartrainingController;
use App\Http\Controllers\InternalWorkExperienceController;
use App\Http\Controllers\TerminationController;

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
Route::middleware('auth:sanctum')->group(function () {
    // AUTH
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/session', [AuthController::class, 'session']);
    Route::resource('sss', SSSContributionController::class);
    Route::resource('witholdingtax', WitholdingTaxContributionController::class);
    Route::resource('leave', LeaveController::class);
    Route::resource('accessibilities', AccessibilitiesController::class);
    Route::resource('settings', SettingsController::class);
    Route::resource('allowance', AllowanceController::class);
    Route::resource('events', EventsController::class);
    Route::resource('announcement', AnnouncementsController::class);
    Route::get('users-employees-list', [UsersController::class, 'get']);
    Route::get('department-list', [DepartmentController::class, 'get']);
    Route::get('user-list', [UsersController::class, 'get']);
    Route::get('allowance-list', [AllowanceController::class, 'get']);
    Route::get('announcement-list', [AnnouncementsController::class, 'currentAnnouncements']);
    Route::resource('philhealth', PhilhealthContributionController::class);
    Route::resource('position', PositionController::class);
    Route::get('position-list', [PositionController::class, 'get']);
    Route::put('update-settings', [SettingsController::class, 'updateSettings']);
    Route::resource('users', UsersController::class);
    Route::resource('approvals', ApprovalsController::class);
    Route::get('get-form-requests/{formname}', [ApprovalsController::class, 'get']);
    Route::post('employee-bulk-upload', [EmployeeBulkUploadController::class, 'bulkUpload']);
    Route::post('employee-bulk-save', [EmployeeBulkUploadController::class, 'bulkSave']);
    Route::resource('departments', DepartmentController::class);
    Route::resource('job-applicants', JobApplicantsController::class);
    Route::get('employee-list', [EmployeeController::class, 'get']);
    Route::resource('pagibig', PagibigContributionController::class);
    Route::resource('manpower-requests', ManpowerRequestController::class);
    Route::post('employee-search', [EmployeeController::class, 'search']);

    Route::resource('employee', EmployeeController::class);
    Route::resource('company-employee', CompanyEmployeeController::class);
    Route::resource('employee-records', EmployeeRecordController::class);
    Route::resource('employee-uploads', EmployeeUploadsController::class);
    Route::resource('employee-address', EmployeeAddressController::class);
    Route::resource('employee-affiliation', EmployeeAffiliationController::class);
    Route::resource('employee-education', EmployeeEducationController::class);
    Route::resource('employee-eligibility', EmployeeEligibilityController::class);
    Route::resource('employee-relatedperson', EmployeeRelatedpersonController::class);
    Route::resource('employee-seminartraining', EmployeeSeminartrainingController::class);

    Route::get('get-request', [ManpowerRequestController::class, 'get']);
    Route::get('get-approve-request', [ManpowerRequestController::class, 'get_approve']);
    Route::put('approve-approval-form/{formid}', [ManpowerRequestController::class, 'approve_approval']);
    Route::put('deny-approval-form/{formid}', [ManpowerRequestController::class, 'deny_approval']);
    Route::get('manpower-for-hiring', [ManpowerRequestController::class, 'get_hiring']);
    Route::get('manpower-with-applicant', [ManpowerRequestController::class, 'get_manpower_with_applicant']);
    Route::get('job-applicants-get', [JobApplicantsController::class, 'get']);

    Route::resource('internalwork-experience', InternalWorkExperienceController::class);
    Route::resource('termination', TerminationController::class);

    Route::resource('salary-grade-level', SalaryGradeLevelController::class);
    Route::post('get-for-hiring', [JobApplicantsController::class, 'get_for_hiring']);
    Route::resource('employee-panrequest', EmployeePersonnelActionNoticeRequestController::class);
    Route::get('get-panrequest', [EmployeePersonnelActionNoticeRequestController::class, 'getpanrequest']);
    Route::get('get-pan-approvals', [EmployeePersonnelActionNoticeRequestController::class, 'getApprovals']);
    Route::post(
        'approve-pan-approvals/{id}',
        [EmployeePersonnelActionNoticeRequestController::class, 'approveApprovals']
    );
});

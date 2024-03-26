<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HMOController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\AllowanceController;
use App\Http\Controllers\ApprovalsController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\HMOMembersController;
use App\Http\Controllers\TerminationController;
use App\Http\Controllers\FailureToLogController;
use App\Http\Controllers\AnnouncementsController;
use App\Http\Controllers\AttendanceLogController;
use App\Http\Controllers\JobApplicantsController;
use App\Http\Controllers\EmployeeRecordController;
use App\Http\Controllers\AccessibilitiesController;
use App\Http\Controllers\CompanyEmployeeController;
use App\Http\Controllers\EmployeeAddressController;
use App\Http\Controllers\EmployeeUploadsController;
use App\Http\Controllers\ManpowerRequestController;
use App\Http\Controllers\SSSContributionController;
use App\Http\Controllers\SalaryGradeLevelController;
use App\Http\Controllers\EmployeeEducationController;
use App\Http\Controllers\EmployeeBulkUploadController;
use App\Http\Controllers\ScheduleDepartmentController;
use App\Http\Controllers\EmployeeAffiliationController;
use App\Http\Controllers\EmployeeEligibilityController;
use App\Http\Controllers\PagibigContributionController;
use App\Http\Controllers\Actions\Pan\ApprovePanApproval;
use App\Http\Controllers\EmployeeRelatedpersonController;
use App\Http\Controllers\InternalWorkExperienceController;
use App\Http\Controllers\PhilhealthContributionController;
use App\Http\Controllers\Actions\Pan\DisapprovePanApproval;
use App\Http\Controllers\EmployeeSeminartrainingController;
use App\Http\Controllers\WitholdingTaxContributionController;
use App\Http\Controllers\Actions\ManpowerRequest\DenyApprovalController;
use App\Http\Controllers\PersonnelActionNoticeRequestController;
use App\Http\Controllers\Actions\ManpowerRequest\ApproveApprovalController;
use App\Http\Controllers\Actions\SalaryGrade\SalaryGradeLevelListController;

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


    Route::resource('manpower-requests', ManpowerRequestController::class);
    Route::prefix('manpower')->group(function () {
        Route::post('approve-approval/{manpower_request}', ApproveApprovalController::class);
        Route::post('deny-approval/{manpower_request}', DenyApprovalController::class);
        Route::get('my-requests', [ManpowerRequestController::class, 'myRequest']);
        Route::get('my-approvals', [ManpowerRequestController::class, 'myApproval']);
        Route::get('for-hiring', [ManpowerRequestController::class, 'forHiring']);
    });




    Route::resource('internalwork-experience', InternalWorkExperienceController::class);
    Route::resource('termination', TerminationController::class);

    Route::prefix('salary')->group(function () {
        Route::resource('salary-grade-level', SalaryGradeLevelController::class);
        Route::get('salary-grade-level-list', SalaryGradeLevelListController::class);
    });

    Route::resource('hmo-members', HMOMembersController::class);
    Route::resource('hmo', HMOController::class);
    Route::resource('schedule', ScheduleDepartmentController::class);
    Route::get('schedules', [ScheduleDepartmentController::class, 'getGroupType']);
    Route::post('get-for-hiring', [JobApplicantsController::class, 'get_for_hiring']);
    Route::put('update-applicant/{id}', [JobApplicantsController::class, 'updateApplicant']);
    Route::prefix('pan')->group(function () {
        Route::resource('resource', PersonnelActionNoticeRequestController::class);
        Route::get('my-request', [PersonnelActionNoticeRequestController::class, 'myRequests']);
        Route::get('my-approvals', [PersonnelActionNoticeRequestController::class, 'myApprovals']);
        Route::post('approve-approval/{pan_request}', ApprovePanApproval::class);
        Route::post('deny-approval/{pan_request}', DisapprovePanApproval::class);
    });

    Route::prefix('attendance')->group(function () {
        Route::resource('logs', AttendanceLogController::class);
        Route::resource('failed-log', FailureToLogController::class);
    });

    Route::prefix('project-monitoring')->group(function () {
        Route::resource('project', ProjectController::class);
    });

    /*     Route::post(
        'approve-pan-approvals/{id}',
        [PersonnelActionNoticeRequestController::class, 'approveApprovals']
    );
    Route::post(
        'disapprove-pan-approvals',
        [
            PersonnelActionNoticeRequestController::class,
            'disapproveApprovals'
        ]
    ); */

    Route::put('update-user', [UsersController::class, 'updateUserCredential']);
});

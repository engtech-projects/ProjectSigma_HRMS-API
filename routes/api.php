<?php

use App\Http\Controllers\EmployeeFacePattern;
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
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\EmployeeAffiliationController;
use App\Http\Controllers\EmployeeEligibilityController;
use App\Http\Controllers\PagibigContributionController;
use App\Http\Controllers\EmployeeRelatedpersonController;
use App\Http\Controllers\InternalWorkExperienceController;
use App\Http\Controllers\PhilhealthContributionController;
use App\Http\Controllers\EmployeeSeminartrainingController;
use App\Http\Controllers\WitholdingTaxContributionController;

use App\Http\Controllers\PersonnelActionNoticeRequestController;
use App\Http\Controllers\EmployeeLeavesController;
use App\Http\Controllers\TravelOrderController;

use App\Http\Controllers\Actions\Approvals\{
    DisapproveApproval,
    ApproveApproval,
};
use App\Http\Controllers\Actions\SalaryGrade\{
    SalaryGradeLevelListController,
};
use App\Http\Controllers\Actions\Attendance\{
    EmployeeDtrController,
};
use App\Http\Controllers\Actions\ProjectMember\{
    AttachProjectEmployee,
    ProjectEmployeeList,
    ProjectMemberList
};
use App\Http\Controllers\Actions\Employee\{
    CountEmployeeDepartmentController,
    CountEmployeeGenderController,
    MonthlyBirthdaysController
};
use App\Http\Controllers\Actions\GeneratePayrollController;
use App\Http\Controllers\Actions\Project\ProjectListController;
use App\Http\Controllers\AttendanceBulkUpload;
use App\Http\Controllers\CashAdvanceController;
use App\Http\Controllers\EmployeeAllowancesController;
use App\Http\Controllers\ExternalWorkExperienceController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\LoansController;
use App\Http\Controllers\OtherDeductionController;
use App\Http\Controllers\OvertimeController;
use App\Http\Controllers\OvertimeEmployeesController;
use Illuminate\Support\Facades\Artisan;

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
    Route::put('update-user', [UsersController::class, 'updateUserCredential']);
    Route::resource('users', UsersController::class);
    Route::resource('accessibilities', AccessibilitiesController::class);
    Route::resource('sss', SSSContributionController::class);
    Route::resource('philhealth', PhilhealthContributionController::class);
    Route::resource('witholdingtax', WitholdingTaxContributionController::class);

    Route::resource('settings', SettingsController::class);
    Route::resource('allowance', AllowanceController::class);
    Route::resource('leave', LeaveController::class);
    Route::resource('events', EventsController::class);
    Route::resource('announcement', AnnouncementsController::class);
    Route::get('announcement-list', [AnnouncementsController::class, 'currentAnnouncements']);
    Route::get('allowance-list', [AllowanceController::class, 'get']);
    Route::prefix("position")->group(function () {
        Route::resource('resource', PositionController::class);
    });
    Route::put('update-settings', [SettingsController::class, 'updateSettings']);
    Route::get('get-form-requests/{formname}', [ApprovalsController::class, 'get']);


    Route::prefix('department')->group(function () {
        Route::resource('resource', DepartmentController::class);
        Route::get('list', [DepartmentController::class, 'get']);
    });

    Route::resource('job-applicants', JobApplicantsController::class);
    Route::resource('pagibig', PagibigContributionController::class);

    Route::prefix("employee")->group(function () {
        Route::get('leave-credits/{id}', [EmployeeController::class, 'getLeaveCredits']);
        Route::get('users-list', [UsersController::class, 'get']);
        Route::post('bulk-upload', [EmployeeBulkUploadController::class, 'bulkUpload']);
        Route::post('bulk-save', [EmployeeBulkUploadController::class, 'bulkSave']);
        Route::get('list', [EmployeeController::class, 'get']);
        Route::post('search', [EmployeeController::class, 'search']);
        Route::resource('resource', EmployeeController::class);
        Route::resource('companyemployment', CompanyEmployeeController::class);
        Route::resource('records', EmployeeRecordController::class);
        Route::resource('uploads', EmployeeUploadsController::class);
        Route::resource('address', EmployeeAddressController::class);
        Route::resource('affiliation', EmployeeAffiliationController::class);
        Route::resource('education', EmployeeEducationController::class);
        Route::resource('eligibility', EmployeeEligibilityController::class);
        Route::resource('relatedperson', EmployeeRelatedpersonController::class);
        Route::resource('seminartraining', EmployeeSeminartrainingController::class);
        Route::resource('internalwork-experience', InternalWorkExperienceController::class);
        Route::resource('termination', TerminationController::class);
        Route::resource('externalwork-experience', ExternalWorkExperienceController::class);

        Route::prefix('statistics')->group(function () {
            Route::get('attendance-infractions', CountEmployeeGenderController::class);
            Route::get('gender', CountEmployeeGenderController::class);
            Route::get('department', CountEmployeeDepartmentController::class);
        });
        Route::prefix('monthly')->group(function () {
            Route::get('birthdays', MonthlyBirthdaysController::class);
            Route::get('lates', [EmployeeController::class, 'getLateThisMonth']);
            Route::get('absences', [EmployeeController::class, 'getAbsenceThisMonth']);
            Route::post('get-late-filter', [EmployeeController::class, 'getFilterLate']);
        });
    });

    Route::resource('approvals', ApprovalsController::class);
    Route::prefix('approvals')->group(function () {
        Route::post('approve/{modelName}/{model}', ApproveApproval::class);
        Route::post('disapprove/{modelName}/{model}', DisapproveApproval::class);
    });


    Route::prefix('manpower')->group(function () {
        Route::resource('resource', ManpowerRequestController::class);
        Route::get('my-requests', [ManpowerRequestController::class, 'myRequest']);
        Route::get('my-approvals', [ManpowerRequestController::class, 'myApproval']);
        Route::get('for-hiring', [ManpowerRequestController::class, 'forHiring']);
    });


    Route::prefix('salary')->group(function () {
        Route::resource('resource', SalaryGradeLevelController::class);
        Route::get('list', SalaryGradeLevelListController::class);
    });

    Route::prefix("hmo")->group(function () {
        Route::resource('resource', HMOController::class);
        Route::resource('members', HMOMembersController::class);
    });
    Route::resource('schedule', ScheduleController::class);
    Route::get('schedules', [ScheduleController::class, 'getGroupType']);
    Route::post('get-for-hiring', [JobApplicantsController::class, 'get_for_hiring']);
    Route::put('update-applicant/{id}', [JobApplicantsController::class, 'updateApplicant']);

    Route::prefix('pan')->group(function () {
        Route::resource('resource', PersonnelActionNoticeRequestController::class);
        Route::get('my-request', [PersonnelActionNoticeRequestController::class, 'myRequests']);
        Route::get('my-approvals', [PersonnelActionNoticeRequestController::class, 'myApprovals']);
    });

    Route::prefix('attendance')->group(function () {
        Route::post('bulk-upload', [AttendanceBulkUpload::class, 'bulkUpload']);
        Route::post('bulk-save', [AttendanceBulkUpload::class, 'bulkSave']);
        Route::resource('log', AttendanceLogController::class);
        Route::resource('failed-log', FailureToLogController::class);
        Route::prefix('failure-to-log')->group(function () {
            Route::get('my-requests', [FailureToLogController::class, 'myRequests']);
            Route::get('my-approvals', [FailureToLogController::class, 'myApprovals']);
        });
        Route::post('dtr', EmployeeDtrController::class);
    });

    Route::prefix('project-monitoring')->group(function () {
        Route::resource('project', ProjectController::class);
        Route::put('attach-employee/{projectMonitoringId}', AttachProjectEmployee::class);
        Route::get('project-employee/{projectMonitoringId}', ProjectEmployeeList::class);
        Route::get('project-member-list/{projectMonitoringId}', ProjectMemberList::class);
    });

    Route::prefix('leave-request')->group(function () {
        Route::resource('resource', EmployeeLeavesController::class);
        Route::get('get-form-request', [EmployeeLeavesController::class, 'myFormRequest']);
        Route::get('my-approvals', [EmployeeLeavesController::class, 'myApprovals']);
    });

    Route::prefix('travelorder-request')->group(function () {
        Route::resource('resource', TravelOrderController::class);
        Route::get('my-request', [TravelOrderController::class, 'myRequests']);
        Route::get('my-approvals', [TravelOrderController::class, 'myApprovals']);
    });

    Route::prefix('loans')->group(function () {
        Route::resource('resource', LoansController::class);
        Route::post('manual-payment/{loan}', [LoansController::class, "loanPayment"]);
    });

    Route::prefix('cash-advance')->group(function () {
        Route::resource('resource', CashAdvanceController::class);
        Route::post('manual-payment/{cash}', [CashAdvanceController::class, "cashAdvancePayment"]);
        Route::get('my-request', [CashAdvanceController::class, 'myRequests']);
        Route::get('my-approvals', [CashAdvanceController::class, 'myApprovals']);
    });

    Route::prefix('overtime')->group(function () {
        Route::resource('resource', OvertimeController::class);
        Route::resource('overtime-employee', OvertimeEmployeesController::class);
        Route::get('my-request', [OvertimeController::class, 'myRequests']);
        Route::get('my-approvals', [OvertimeController::class, 'myApprovals']);
    });

    Route::prefix('other-deduction')->group(function () {
        Route::resource('resource', OtherDeductionController::class);
        Route::post('manual-payment/{cash}', [OtherDeductionController::class, "cashAdvancePayment"]);
    });

    Route::prefix('images')->group(function () {
        Route::prefix('upload')->group(function () {
            Route::post('digital-signature/{id}', [ImageController::class, "uploadDigitalSignature"]);
            Route::post('profile-picture/{id}', [ImageController::class, "uploadProfileImage"]);
        });
    });

    Route::prefix('employee-allowance')->group(function () {
        Route::post('view-allowance', [EmployeeAllowancesController::class, "viewAllowanceRecords"]);
        Route::resource('resource', EmployeeAllowancesController::class);
    });

    Route::prefix('payroll')->group(function () {
        Route::post('generate-payroll', GeneratePayrollController::class);
    });
});



if (config()->get('app.artisan') == 'true') {
    Route::prefix('artisan')->group(function () {
        Route::get('storage', function () {
            Artisan::call("storage:link");
            return "success";
        });
    });
}

//public
Route::prefix('face-pattern')->group(function () {
    Route::resource('resource', EmployeeFacePattern::class);
});
// Route::prefix('project-monitoring')->group(function () {
//     Route::get('list', ProjectController::class, 'getProject');
// });
Route::prefix("department")->group(function () {
    Route::get('list', [DepartmentController::class, 'getList']);
});
Route::prefix('attendance')->group(function () {
    Route::get('facial-list', [AttendanceLogController::class, 'facialAttendanceList']);
    Route::post('facial', [AttendanceLogController::class, 'facialAttendance']);
});

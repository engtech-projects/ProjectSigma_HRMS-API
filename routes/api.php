<?php

use App\Http\Controllers\AbsentController;
use App\Http\Controllers\Actions\Employee\CountAbsentLateController;
use App\Http\Controllers\CashAdvancePaymentsController;
use App\Http\Controllers\EmployeeFacePattern;
use App\Http\Controllers\LateController;
use App\Http\Controllers\LoanPaymentsController;
use App\Http\Controllers\ReportController;
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
    EmployeeDtrControllerV2,
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
use App\Http\Controllers\Actions\Project\ProjectListController;
use App\Http\Controllers\AllowanceRequestController;
use App\Http\Controllers\ApiServiceController;
use App\Http\Controllers\AttendanceBulkUpload;
use App\Http\Controllers\AttendancePortalController;
use App\Http\Controllers\CashAdvanceController;
use App\Http\Controllers\EmployeeAllowancesController;
use App\Http\Controllers\ExternalWorkExperienceController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\LoansController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\OtherDeductionController;
use App\Http\Controllers\OtherDeductionPaymentsController;
use App\Http\Controllers\OvertimeController;
use App\Http\Controllers\OvertimeEmployeesController;
use App\Http\Controllers\PayrollRecordController;
use App\Http\Controllers\ProjectListController as ViewProjectListController;
use App\Http\Controllers\RequestSalaryDisbursementController;
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
    Route::get('user-account-by-employee-id/{id}', [UsersController::class, 'getUserAccountByEmployeeId']);
    Route::resource('accessibilities', AccessibilitiesController::class);
    // HRMS SETUPS
    Route::resource('sss', SSSContributionController::class);
    Route::resource('philhealth', PhilhealthContributionController::class);
    Route::resource('pagibig', PagibigContributionController::class);
    Route::resource('witholdingtax', WitholdingTaxContributionController::class);
    Route::resource('settings', SettingsController::class);
    Route::resource('allowance', AllowanceController::class);
    Route::get('allowance-list', [AllowanceController::class, 'get']);
    Route::resource('leave', LeaveController::class);
    Route::resource('events', EventsController::class);
    Route::prefix("position")->group(function () {
        Route::resource('resource', PositionController::class)->names("setupPosition");
        Route::get('list', [PositionController::class, 'get']);
    });
    Route::put('update-settings', [SettingsController::class, 'updateSettings']);
    Route::prefix('department')->group(function () {
        Route::resource('resource', DepartmentController::class)->names("setupDepartment");
        Route::get('list', [DepartmentController::class, 'get']);
    });
    Route::prefix('salary')->group(function () {
        Route::resource('resource', SalaryGradeLevelController::class)->names("setupSalary");
        Route::get('list', SalaryGradeLevelListController::class);
    });
    // APPROVALS
    Route::resource('approvals', ApprovalsController::class);
    Route::prefix('approvals')->group(function () {
        Route::post('approve/{modelName}/{model}', ApproveApproval::class);
        Route::post('disapprove/{modelName}/{model}', DisapproveApproval::class);
    });
    Route::get('get-form-requests/{formname}', [ApprovalsController::class, 'get']);
    // HRMS REQUESTS/TRANSACTIONS WITH APPROVALS
    Route::prefix('manpower')->group(function () {
        Route::resource('resource', ManpowerRequestController::class)->names("requestManpower");
        Route::get('my-requests', [ManpowerRequestController::class, 'myRequest']);
        Route::get('my-approvals', [ManpowerRequestController::class, 'myApproval']);
        Route::get('for-hiring', [ManpowerRequestController::class, 'forHiring']);
    });
    Route::prefix('pan')->group(function () {
        Route::resource('resource', PersonnelActionNoticeRequestController::class)->names("requestPan");
        Route::get('my-request', [PersonnelActionNoticeRequestController::class, 'myRequests']);
        Route::get('my-approvals', [PersonnelActionNoticeRequestController::class, 'myApprovals']);
        Route::get("generate-company-id-num", [PersonnelActionNoticeRequestController::class, "generateIdNum"]);
    });
    Route::prefix('leave-request')->group(function () {
        Route::resource('resource', EmployeeLeavesController::class)->names("requestLeaves");
        Route::get('get-form-request', [EmployeeLeavesController::class, 'myFormRequest']);
        Route::get('my-request', [EmployeeLeavesController::class, 'myRequests']);
        Route::get('my-approvals', [EmployeeLeavesController::class, 'myApprovals']);
    });
    Route::prefix('overtime')->group(function () {
        Route::resource('resource', OvertimeController::class)->names("requestOvertime");
        Route::resource('overtime-employee', OvertimeEmployeesController::class);
        Route::get('my-request', [OvertimeController::class, 'myRequests']);
        Route::get('my-approvals', [OvertimeController::class, 'myApprovals']);
    });
    Route::prefix('travelorder-request')->group(function () {
        Route::resource('resource', TravelOrderController::class)->names("requestTravelorder");
        Route::get('my-request', [TravelOrderController::class, 'myRequests']);
        Route::get('my-approvals', [TravelOrderController::class, 'myApprovals']);
    });
    Route::prefix('loans')->group(function () {
        Route::resource('resource', LoansController::class)->names("requestLoans");
        Route::get('ongoing', [LoansController::class, 'ongoing']);
        Route::get('paid', [LoansController::class, 'paid']);
        Route::get('payments', [LoanPaymentsController::class, 'index']);
        Route::post('manual-payment/{loan}', [LoansController::class, "loanPayment"]);
    });
    Route::prefix('cash-advance')->group(function () {
        Route::resource('resource', CashAdvanceController::class)->names("requestCashadvance");
        Route::post('manual-payment/{cash}', [CashAdvanceController::class, "cashAdvancePayment"]);
        Route::get('my-request', [CashAdvanceController::class, 'myRequests']);
        Route::get('my-approvals', [CashAdvanceController::class, 'myApprovals']);
        Route::get('ongoing', [CashAdvanceController::class, 'getOngoingCashAdvance']);
        Route::get('paid', [CashAdvanceController::class, 'getPaidCashAdvance']);
        Route::get('payments', [CashAdvancePaymentsController::class, 'index']);
    });
    Route::prefix('other-deduction')->group(function () {
        Route::resource('resource', OtherDeductionController::class)->names("requestOtherdeduction");
        Route::get('ongoing', [OtherDeductionController::class, 'ongoing']);
        Route::get('paid', [OtherDeductionController::class, 'paid']);
        Route::get('payments', [OtherDeductionPaymentsController::class, 'index']);
        Route::post('manual-payment/{oded}', [OtherDeductionController::class, "cashAdvancePayment"]);
    });
    Route::prefix("allowance-request")->group(function () {
        Route::post("draft", [AllowanceRequestController::class, "generateDraft"]);
        Route::resource('resource', AllowanceRequestController::class)->names("requestAllowance");
        Route::get('my-requests', [AllowanceRequestController::class, 'myRequest']);
        Route::get('my-approvals', [AllowanceRequestController::class, 'myApproval']);
    });
    Route::prefix('employee-allowance')->group(function () {
        Route::get('view-allowance', [EmployeeAllowancesController::class, "viewAllowanceRecords"]);
    });
    Route::prefix('payroll')->group(function () {
        Route::post('generate-payroll', [PayrollRecordController::class, 'generate']);
        Route::post('create-payroll', [PayrollRecordController::class, 'store']);
        Route::get('my-requests', [PayrollRecordController::class, 'myRequest']);
        Route::get('my-approvals', [PayrollRecordController::class, 'myApproval']);
        Route::resource('resource', PayrollRecordController::class)->names("requestPayroll");
        Route::get('records', [PayrollRecordController::class, 'payrollRecords']);
    });
    Route::prefix('salary-disbursement')->group(function () {
        Route::post('draft', [RequestSalaryDisbursementController::class, 'generateDraft']);
        Route::resource('resource', RequestSalaryDisbursementController::class)->names("requestSalaryDisbursement");
        Route::get('my-requests', [RequestSalaryDisbursementController::class, 'myRequests']);
        Route::get('my-approvals', [RequestSalaryDisbursementController::class, 'myApprovals']);
        Route::get('payslip-ready', [RequestSalaryDisbursementController::class, 'payslipReady']);
        Route::get('payslip-ready/{requestSalaryDisbursement}', [RequestSalaryDisbursementController::class, 'payslipReadyShow']);
    });
    // NON APPROVAL TRANSACTIONS/FUNCTIONS
    Route::resource('announcement', AnnouncementsController::class);
    Route::prefix("hmo")->group(function () {
        Route::resource('resource', HMOController::class)->names("setupHmo");
        Route::resource('members', HMOMembersController::class);
    });
    Route::post('get-for-hiring', [JobApplicantsController::class, 'get_for_hiring']);
    Route::resource('job-applicants', JobApplicantsController::class);
    Route::put('update-applicant/{id}', [JobApplicantsController::class, 'updateApplicant']);
    Route::resource('schedule', ScheduleController::class);
    Route::get('schedules', [ScheduleController::class, 'getGroupType']);
    // EMPLOYEE DETAILS AND OTHERS
    Route::prefix("employee")->group(function () {
        Route::get('leave-credits/{employee}', [EmployeeController::class, 'getLeaveCredits']);
        Route::get('users-list', [UsersController::class, 'get']);
        Route::post('bulk-upload', [EmployeeBulkUploadController::class, 'bulkUpload']);
        Route::post('bulk-save', [EmployeeBulkUploadController::class, 'bulkSave']);
        Route::get('list', [EmployeeController::class, 'get']);
        Route::post('search', [EmployeeController::class, 'search']);
        Route::resource('resource', EmployeeController::class)->names("employees");
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
            Route::get('attendance-infractions', CountAbsentLateController::class);
            Route::get('gender', CountEmployeeGenderController::class);
            Route::get('department', CountEmployeeDepartmentController::class);
        });
        Route::prefix('monthly')->group(function () {
            Route::get('birthdays', MonthlyBirthdaysController::class);
            Route::get('lates', [LateController::class, 'getLateThisMonth']);
            Route::get('absences', [AbsentController::class, 'getAbsenceThisMonth']);
            Route::post('get-late-filter', [EmployeeController::class, 'getFilterLate']);
        });
    });
    Route::prefix('images')->group(function () {
        Route::prefix('upload')->group(function () {
            Route::post('digital-signature/{id}', [ImageController::class, "uploadDigitalSignature"]);
            Route::post('profile-picture/{id}', [ImageController::class, "uploadProfileImage"]);
        });
    });
    // ATTENDANCE
    Route::prefix('attendance')->group(function () {
        Route::post('bulk-upload', [AttendanceBulkUpload::class, 'bulkUpload']);
        Route::post('bulk-save', [AttendanceBulkUpload::class, 'bulkSave']);
        Route::resource('log', AttendanceLogController::class);
        Route::post('qr', [AttendanceLogController::class, 'qrAttendance']);
        Route::resource('failed-log', FailureToLogController::class);
        Route::prefix('attendanceQR')->group(function () {
            Route::post('qr', [AttendanceLogController::class, 'qrAttendance']);
        });
        Route::get('all-attendance-logs', [AttendanceLogController::class, 'allAttendanceLogs']);
        Route::prefix('failure-to-log')->group(function () {
            Route::get('my-requests', [FailureToLogController::class, 'myRequests']);
            Route::get('my-approvals', [FailureToLogController::class, 'myApprovals']);
        });
        Route::get('dtr', EmployeeDtrController::class);
    });
    Route::prefix('attendance-portal')->group(function () {
        Route::resource('resource', AttendancePortalController::class)->names("setupAttendancePortals");
    });
    Route::prefix('face-pattern')->group(function () {
        Route::resource('resource', EmployeeFacePattern::class)->names("employeeFaces");
    });
    // REPORTS
    Route::prefix('reports')->group(function () {
        Route::get('sss-employee-remittance', [ReportController::class, 'sssEmployeeRemittanceGenerate']);
        Route::get('pagibig-employee-remittance', [ReportController::class, 'pagibigEmployeeRemittanceGenerate']);
        Route::get('philhealth-employee-remittance', [ReportController::class, 'philhealthEmployeeRemittanceGenerate']);
        Route::get('sss-group-remittance', [ReportController::class, 'sssGroupRemittanceGenerate']);
        Route::get('pagibig-group-remittance', [ReportController::class, 'pagibigGroupRemittanceGenerate']);
        Route::get('philhealth-group-remittance', [ReportController::class, 'philhealthGroupRemittanceGenerate']);
        Route::get('sss-remittance-summary', [ReportController::class, 'sssRemittanceSummary']);
        Route::get('pagibig-remittance-summary', [ReportController::class, 'pagibigRemittanceSummary']);
        Route::get('philhealth-remittance-summary', [ReportController::class, 'philhealthRemittanceSummary']);
    });
    // PROJECT
    Route::prefix('project-monitoring')->group(function () {
        Route::resource('project', ProjectController::class);
        Route::get('list', ProjectListController::class);
        Route::put('attach-employee/{projectMonitoringId}', AttachProjectEmployee::class);
        Route::get('project-employee/{projectMonitoringId}', ProjectEmployeeList::class);
        Route::get('project-member-list/{projectMonitoringId}', ProjectMemberList::class);
    });
    // NOTIFICATIONS
    Route::prefix('notifications')->group(function () {
        Route::get('unread', [NotificationsController::class, "getUnreadNotifications"]);
        Route::get('unread-stream', [NotificationsController::class, "getUnreadNotificationsStream"]);
        Route::get('all', [NotificationsController::class, "getNotifications"]);
        Route::put('read/{notif}', [NotificationsController::class, "readNotification"]);
        Route::put('read-all', [NotificationsController::class, "readAllNotifications"]);
        Route::put('unread/{notif}', [NotificationsController::class, "unreadNotification"]);
        Route::post('services-notify/{user}', [NotificationsController::class, "addNotification"]);
    });
    // SERVICES ROUTES
    Route::prefix('services')->group(function () {
        Route::get("format-approvals", [ApiServiceController::class, "formatApprovals"]);
        Route::get("user-employees", [ApiServiceController::class, "getUserEmployees"]);
    });
    // Version 2 Optimization
    Route::prefix("v2")->group(function () {
        Route::prefix('payroll')->group(function () {
            Route::post('generate-payroll', [PayrollRecordController::class, 'generateV2']);
        });
        Route::prefix('attendance')->group(function () {
            Route::get('dtr', EmployeeDtrControllerV2::class);
        });
    });
});
// ATTENDANCE PORTAL TOKEN AUTH
Route::middleware('portal_in')->group(function () {
    Route::prefix('attendance')->group(function () {
        Route::get('current-date-time', [AttendanceLogController::class, 'getCurrentDateTime']);
        Route::post('facial', [AttendanceLogController::class, 'facialAttendance']);
        Route::get('facial-list', [AttendanceLogController::class, 'facialAttendanceList']);
        Route::get('portal-session', [AttendancePortalController::class, "attendancePortalSession"]);
        Route::get('today-logs', [AttendanceLogController::class, "getToday"]);
    });
});

//public
Route::prefix("department")->group(function () {
    Route::get('list/v2', [DepartmentController::class, 'get']); // NEED TO CHECK WHAT FOR
});
Route::resource('employee/resource/v2', EmployeeController::class); // NEED TO CHECK WHAT FOR
Route::prefix('project-monitoring')->group(function () {
    Route::get('lists', ViewProjectListController::class); // NEED TO CHECK WHAT FOR
});
Route::get('current-announcements', [AnnouncementsController::class, 'currentAnnouncements']);
// SYSTEM SETUP ROUTES
if (config()->get('app.artisan') == 'true') {
    Route::prefix('artisan')->group(function () {
        Route::get('storage', function () {
            Artisan::call("storage:link");
            return "success";
        });
    });
}

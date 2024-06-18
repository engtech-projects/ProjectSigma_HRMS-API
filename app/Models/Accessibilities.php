<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Accessibilities extends Model
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'id',
        'accessibilities_name',
        'updated_at',
        'created_at',
    ];


    // public const accessibilities_name = "hrms:dashboard";
    // public const accessibilities_name = "hrms:announcement";
    // public const accessibilities_name = "hrms:attendance_attendance portal";
    // public const accessibilities_name = "hrms:attendance_dtr";
    // public const accessibilities_name = "hrms:attendance_failure to log";
    // public const accessibilities_name = "hrms:attendance_face recognition";
    // public const accessibilities_name = "hrms:attendance_attendance login";
    // public const accessibilities_name = "hrms:attendance_qr";
    // public const accessibilities_name = "hrms:event calendar";
    public const HRMS_EMPLOYEE_201_EDIT = "hrms:employee_201_edit";
    // public const accessibilities_name = "hrms:employee_pan";
    // public const accessibilities_name = "hrms:employee_onboarding";
    // public const accessibilities_name = "hrms:employee_manpower request";
    // public const accessibilities_name = "hrms:setup_user account";
    // public const accessibilities_name = "hrms:setup_department";
    // public const accessibilities_name = "hrms:setup_approvals";
    // public const accessibilities_name = "hrms:hmo";
    // public const accessibilities_name = "hrms:setup_pag-ibig";
    // public const accessibilities_name = "hrms:setup_philhealth";
    // public const accessibilities_name = "hrms:setup_sss";
    // public const accessibilities_name = "hrms:setup_withholding tax";
    // public const accessibilities_name = "hrms:setup_leaves";
    // public const accessibilities_name = "hrms:leave";
    // public const accessibilities_name = "hrms:loans and advances_cash advance";
    // public const accessibilities_name = "hrms:loans and advances_loans";
    // public const accessibilities_name = "hrms:loans and advances_other deductions";
    // public const accessibilities_name = "hrms:overtime";
    // public const accessibilities_name = "hrms:payroll_generate payroll";
    // public const accessibilities_name = "hrms:payroll_13th month";
    // public const accessibilities_name = "hrms:payroll_allowance";
    // public const accessibilities_name = "hrms:payroll_payroll record";
    // public const accessibilities_name = "hrms:reports";
    // public const accessibilities_name = "hrms:schedule_department";
    // public const accessibilities_name = "hrms:schedule_employee";
    // public const accessibilities_name = "hrms:schedule_project";
    // public const accessibilities_name = "hrms:project members";
    // public const accessibilities_name = "hrms:travel order";
    // public const accessibilities_name = "hrms:setup_division";
    // public const accessibilities_name = "hrms:setup_position";
    // public const accessibilities_name = "hrms:setup_allowance";
    // public const accessibilities_name = "hrms:setup_settings";
    public const HRMS_SETUP_SALARY_GRADE = "hrms:setup_salary grade";
    // public const accessibilities_name = "accounting:chart of accounts";
    // public const accessibilities_name = "accounting:books";
    // public const accessibilities_name = "accounting:transaction type";
    // public const accessibilities_name = "accounting:document Series";
    // public const accessibilities_name = "accounting:posting period";
    // public const accessibilities_name = "accounting:account groups";
    // public const accessibilities_name = "accounting:stake holder";
    // public const accessibilities_name = "project_monitoring:dashboard";
    // public const accessibilities_name = "project_monitoring:projects";
    // public const accessibilities_name = "hrms:dashboard_announcement";
    // public const accessibilities_name = "hrms:dashboard_birthday";
    // public const accessibilities_name = "hrms:dashboard_lates";
    // public const accessibilities_name = "hrms:dashboard_absent";
    // public const accessibilities_name = "hrms:dashboard_absent_chart";
    // public const accessibilities_name = "hrms:dashboard_assignment_location_chart";
    // public const accessibilities_name = "hrms:dashboard_gender_chart";
    // public const accessibilities_name = "hrms:announcement_form";
    // public const accessibilities_name = "hrms:announcement_list";
    // public const accessibilities_name = "accounting:dashboard";
    // public const accessibilities_name = "";
    // public const accessibilities_name = "";
    // public const accessibilities_name = "";
    // public const accessibilities_name = "";
    // public const accessibilities_name = "";
    // public const accessibilities_name = "";
    // public const accessibilities_name = "hrms:employee_201_pis";
    // public const accessibilities_name = "hrms:employee_201_staff information sheet";
    // public const accessibilities_name = "hrms:employee_201_documents and memos";
    // public const accessibilities_name = "hrms:employee_201_id";
    // public const accessibilities_name = "hrms:employee_pan_form";
    // public const accessibilities_name = "hrms:employee_pan_all request";
    // public const accessibilities_name = "hrms:employee_pan_approval";
    // public const accessibilities_name = "hrms:employee_pan_my request";
    // public const accessibilities_name = "hrms:employee_manpower request_form";
    // public const accessibilities_name = "hrms:employee_manpower request_all request";
    // public const accessibilities_name = "hrms:employee_manpower request_approval";
    // public const accessibilities_name = "hrms:employee_manpower request_my request";
    // public const accessibilities_name = "hrms:loans and advances_cash advance_forms";
    // public const accessibilities_name = "hrms:loans and advances_cash advance_list";
    // public const accessibilities_name = "hrms:loans and advances_cash advance_approvals";
    // public const accessibilities_name = "hrms:loans and advances_loans_forms";
    // public const accessibilities_name = "hrms:loans and advances_loans_list";
    // public const accessibilities_name = "hrms:loans and advances_loans_payments";
    // public const accessibilities_name = "hrms:loans and advances_other deductions_forms";
    // public const accessibilities_name = "hrms:loans and advances_other deductions_list";
    // public const accessibilities_name = "hrms:leaves and overtime_leave_form";
    // public const accessibilities_name = "hrms:leaves and overtime_leave_list";
    // public const accessibilities_name = "hrms:leaves and overtime_leave_my approvals";
    // public const accessibilities_name = "hrms:leaves and overtime_overtime_form";
    // public const accessibilities_name = "hrms:leaves and overtime_overtime_list";
    // public const accessibilities_name = "hrms:leaves and overtime_overtime_my request";
    // public const accessibilities_name = "hrms:leaves and overtime_overtime_my approvals";
    // public const accessibilities_name = "hrms:leaves and overtime_travel order_form";
    // public const accessibilities_name = "hrms:leaves and overtime_travel order_list";
    // public const accessibilities_name = "hrms:leaves and overtime_travel order_my request";
    // public const accessibilities_name = "hrms:leaves and overtime_travel order_my approvals";
    // public const accessibilities_name = "hrms:attendance_attendance logs";
    // public const accessibilities_name = "hrms:employee_job applicant";
    // public const accessibilities_name = "hrms:attendance_biometrics";
}

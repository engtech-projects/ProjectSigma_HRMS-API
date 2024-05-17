<?php

namespace App\Http\Controllers;

use Exception;
use App\Helpers;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\PayrollRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Services\EmployeeService;
use App\Http\Requests\GeneratePayrollRequest;
use App\Http\Services\Payroll\PayrollService;
use App\Exceptions\TransactionFailedException;
use App\Http\Requests\StorePayrollRecordRequest;
use App\Models\PayrollDetail;

class PayrollRecordController extends Controller
{

    protected $payrollService;
    public function __construct(PayrollService $payrollService)
    {
        $this->payrollService = $payrollService;
    }

    public function generate(GeneratePayrollRequest $request)
    {
        $filters = $request->validated();

        $periodDates = Helpers::dateRange([
            'period_start' => $filters["cutoff_start"], 'period_end' => $filters["cutoff_end"]
        ]);
        $employeeDtr = Employee::whereIn('id', $filters['employee_ids'])->get();
        $result = collect($employeeDtr)->map(function ($employee) use ($periodDates, $filters) {
            $employee["payroll_records"] = $this->payrollService->generatePayroll($periodDates, $filters, $employee);
            unset($employee["current_employment"]);
            return $employee;
        });

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully fetched.',
            'data' => $result
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePayrollRecordRequest $request)
    {
        $attribute = $request->validated();

        try {
            DB::transaction(function () use ($attribute) {
                $payroll = PayrollRecord::create($attribute);
                $details = [

                    "payroll_record_id" => $payroll->id,
                    "employee_id" => 1,
                    "regular_hours" => 1,
                    "rest_hours" => 1,
                    "regular_holiday_hours" => 1,
                    "special_holiday_hours" => 1,
                    "regular_overtime" => 1,
                    "rest_overtime" => 1,
                    "regular_holiday_overtime" => 1,
                    "special_holiday_overtime" => 1,
                    "regular_pay" => 1,
                    "rest_pay" => 1,
                    "regular_holiday_pay" => 1,
                    "special_holiday_pay" => 1,
                    "regular_ot_pay" => 1,
                    "rest_ot_pay" => 1,
                    "regular_holiday_ot_pay" => 1,
                    "special_holiday_ot_pay" => 1,
                    "gross_pay" => 1,
                    "late_hours" => 1,
                    "sss_employee_contribution" => 1,
                    "sss_employer_contribution"  => 1,
                    "sss_employee_compensation" => 1,
                    "sss_employer_compensation" => 1,
                    "philhealth_employee_contribution" => 1,
                    "philhealth_employer_contribution" => 1,
                    "pagibig_employee_contribution" => 1,
                    "pagibig_employer_contribution" => 1,
                    "pagibig_employee_compensation" => 1,
                    "pagibig_employer_compensation" => 1,
                    "withholdingtax_contribution" => 1,
                    "total_deduct" => 1,
                    "net_pay" => 1,


                ];
                $payroll->payroll_details()->create($details);
            });
        } catch (Exception $e) {
            throw new TransactionFailedException("Transaction failed.", 500, $e);
        }

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully saved.',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

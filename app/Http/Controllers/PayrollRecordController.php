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
use App\Models\Department;
use App\Models\PayrollDetail;
use App\Models\Project;

class PayrollRecordController extends Controller
{

    protected $employeeService;
    protected $payrollService;
    public function __construct(EmployeeService $employeeService, PayrollService $payrollService)
    {
        $this->employeeService = $employeeService;
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
            $employee["payroll_records"] = $this->employeeService->generatePayroll($periodDates, $filters, $employee);
            $employee->current_employment['position'] = $employee->current_employment->position;
            return $employee;
        });

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully fetched.',
            'data' => [
                ...$filters,
                "project" => Project::find($filters['project_id']),
                "department" => Department::find($filters['department_id']),
                "payroll" => $result
            ]
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
                foreach($attribute["payroll"] as $payrollData){
                    $empPayroll = $payroll->payroll_details()->createMany($payrollData);
                    // $empPayroll->payroll_detail_deduction()->create(collect($payrollData['cash_advance'])->map(function($dedData){
                    //     return [
                    //         ...$dedData,
                    //         "type" => 'Cash Advance',
                    //     ];
                    // }));
                    // $empPayroll->payroll_detail_deduction()->create(collect($payrollData['loans'])->map(function($dedData){
                    //     return [
                    //         ...$dedData,
                    //         "type" => 'Loan',
                    //     ];
                    // }));
                    // $empPayroll->payroll_detail_deduction()->create(collect($payrollData['other_deduction'])->map(function($dedData){
                    //     return [
                    //         ...$dedData,
                    //         "type" => 'Other Deduction',
                    //     ];
                    // }));
                }
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

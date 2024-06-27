<?php

namespace App\Http\Controllers;

use App\Enums\PayrollDetailsDeductionType;
use App\Enums\RequestStatusType;
use App\Enums\PostingStatusType;
use App\Enums\LoanPaymentsType;
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
use App\Models\PayrollDetailDeduction;
use App\Models\CashAdvancePayments;
use App\Models\LoanPayments;
use App\Models\OtherDeduction;
use App\Models\PayrollDetail;
use App\Models\PayrollDetailsAdjustment;
use App\Models\PayrollDetailsCharging;
use App\Models\Project;
use Carbon\Carbon;

class PayrollRecordController extends Controller
{
    // 'Cash Advance','Loan','Other Deduction','Others'
    public const CASHADVANCE = "App\Models\CashAdvance";
    public const LOANS = "App\Models\Loans";
    public const OTHERDEDUCTION = "App\Models\OtherDeduction";

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
                "project" => Project::find($filters['project_id'] ?? null),
                "department" => Department::find($filters['department_id'] ?? null),
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
        $attribute['request_status'] = RequestStatusType::PENDING->value;
        try {
            DB::transaction(function () use ($attribute) {
                $payroll = PayrollRecord::create($attribute);
                foreach($attribute["payroll_details"] as $payrollData){
                    $empPayrollDetail = $payroll->payroll_details()->create($payrollData);
                    $empPayrollDetail->adjustments()->createMany($payrollData["adjustment"]);
                    PayrollDetailDeduction::create($this->setPayrollDetails($payrollData["deductions"], $empPayrollDetail));
                    // PayrollDetailsCharging::create($this->setPayrollDetails($payrollData["charging"], $empPayrollDetail));
                }
            });
        } catch (Exception $e) {
            throw new TransactionFailedException("Transaction failed.", 500, $e);
        }

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully saved.',
        ], JsonResponse::HTTP_OK);
    }

    public function setPayrollDetails($deductions, $empPayrollDetail){
        foreach ($deductions as $data) {
            $paymentStore = [
                "posting_status" => PostingStatusType::NOTPOSTED->value,
                "payment_type" => LoanPaymentsType::PAYROLL->value,
                "date_paid" => Carbon::now()->format('Y-m-d'),
                "amount_paid" => $data["amount"],
            ];
            switch ($data["type"]) {
                case PayrollDetailsDeductionType::CASHADVANCE->value:
                    $paymentStore["cashadvance_id"] = $data["charge_id"];
                    $thisPayment = CashAdvancePayments::create($paymentStore);
                    return $this->adjustChargingData($data, $thisPayment, $empPayrollDetail);
                    break;
                case PayrollDetailsDeductionType::LOAN->value:
                    $paymentStore["loans_id"] = $data["charge_id"];
                    $thisPayment = LoanPayments::create($paymentStore);
                    return $this->adjustChargingData($data, $thisPayment, $empPayrollDetail);
                break;
                case PayrollDetailsDeductionType::OTHERDEDUCTION->value:
                    $paymentStore["otherdeduction_id"] = $data["charge_id"];
                    $thisPayment = OtherDeduction::create($paymentStore);
                    return $this->adjustChargingData($data, $thisPayment, $empPayrollDetail);
                break;
            }
        }
    }

    public function adjustChargingData($data, $thisPayment, $empPayrollDetail){
        $data["deduction_type"] = $this->getChargingModel($data["type"]);
        $data["deduction_id"] = $thisPayment->id;
        $data["charge_type"] = $this->getChargingModel($data["type"]);
        $data["charge_id"] = $thisPayment->id;
        $data["payroll_details_id"] = $empPayrollDetail->id;
        return $data;
    }

    public function getChargingModel($type)
    {
        switch ($type) {
            case PayrollDetailsDeductionType::CASHADVANCE->value:
                return PayrollRecordController::CASHADVANCE;
            break;
            case PayrollDetailsDeductionType::LOAN->value:
                return PayrollRecordController::LOANS;
            break;
            case PayrollDetailsDeductionType::OTHERDEDUCTION->value:
                return PayrollRecordController::OTHERDEDUCTION;
            break;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $myRequest = PayrollRecord::with('payroll_details')->where('id',$id)->get()->append(['charging_name']);
        return new JsonResponse([
            'success' => true,
            'message' => 'Payrollrecord request fetched.',
            'data' => $myRequest
        ]);
    }

    public function index()
    {
        $myRequest = $this->payrollService->getAll();
        return new JsonResponse([
            'success' => true,
            'message' => 'Payrollrecord request fetched.',
            'data' => $myRequest
        ]);
    }

    public function myRequest()
    {
        $myRequest = $this->payrollService->getAll();
        if ($myRequest->isEmpty()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'No data found.',
            ], JsonResponse::HTTP_OK);
        }
        return new JsonResponse([
            'success' => true,
            'message' => 'Payrollrecord Request fetched.',
            'data' => $myRequest
        ]);
    }

    /**
     * Show all requests to be approved/reviewed by current user
     */
    public function myApproval()
    {
        $myApproval = $this->payrollService->getMyApprovals();
        if ($myApproval->isEmpty()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'No data found.',
            ], JsonResponse::HTTP_OK);
        }
        return new JsonResponse([
            'success' => true,
            'message' => 'Payrollrecord Request fetched.',
            'data' => $myApproval
        ]);
    }
}

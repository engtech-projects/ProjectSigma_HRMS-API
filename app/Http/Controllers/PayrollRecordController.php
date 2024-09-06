<?php

namespace App\Http\Controllers;

use App\Enums\PayrollDetailsDeductionType;
use App\Enums\RequestStatusType;
use App\Enums\PostingStatusType;
use App\Enums\LoanPaymentsType;
use App\Helpers;
use App\Models\Employee;
use App\Models\PayrollRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Services\EmployeeService;
use App\Http\Requests\GeneratePayrollRequest;
use App\Http\Services\Payroll\PayrollService;
use App\Http\Requests\StorePayrollRecordRequest;
use App\Http\Resources\PayrollRequestResource;
use App\Models\Department;
use App\Models\PayrollDetailDeduction;
use App\Models\CashAdvancePayments;
use App\Models\LoanPayments;
use App\Models\OtherDeductionPayments;
use App\Models\Project;
use App\Models\Users;
use App\Notifications\PayrollRequestForApproval;
use App\Utils\PaginateResourceCollection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PayrollRecordController extends Controller
{
    // 'Cash Advance','Loan','Other Deduction','Others'
    public const CASHADVANCE = "App\Models\CashAdvance";
    public const LOANS = "App\Models\Loans";
    public const OTHERDEDUCTION = "App\Models\OtherDeduction";
    public const CASHADVANCE_PAYMENT = CashAdvancePayments::class;
    public const LOANS_PAYMENT = LoanPayments::class;
    public const OTHERDEDUCTION_PAYMENT = OtherDeductionPayments::class;

    protected $employeeService;
    protected $payrollService;
    public function __construct(EmployeeService $employeeService, PayrollService $payrollService)
    {
        $this->employeeService = $employeeService;
        $this->payrollService = $payrollService;
    }

    public function generate(GeneratePayrollRequest $request)
    {
        ini_set('max_execution_time', '999999');
        $filters = $request->validated();

        $periodDates = Helpers::dateRange([
            'period_start' => $filters["cutoff_start"], 'period_end' => $filters["cutoff_end"]
        ]);
        $employeeDtr = Employee::whereIn('id', $filters['employee_ids'])->get();
        try {
            $result = collect($employeeDtr)->map(function ($employee) use ($periodDates, $filters) {
                $employee["payroll_records"] = $this->employeeService->generatePayroll($periodDates, $filters, $employee);
                $employee->current_employment['position'] = $employee->current_employment->position;
                return $employee;
            });
        } catch (\Throwable $th) {
            Log::error($th);
            return new JsonResponse([
                'success' => false,
                'message' => $th->getMessage(),
            ], 400);
        }
        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully fetched.',
            'data' => [
                ...$filters,
                "project" => Project::find($filters['project_id'] ?? null),
                "department" => Department::find($filters['department_id'] ?? null),
                "payroll_details" => $result
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
        $attribute['created_by'] = auth()->user()->id;
        // try {
        DB::transaction(function () use ($attribute) {
            $payroll = PayrollRecord::create($attribute);
            foreach($attribute["payroll_details"] as $employeePayrollData) {
                $empPayrollDetail = $payroll->payroll_details()->create($employeePayrollData);
                $empPayrollDetail->adjustments()->createMany($employeePayrollData["adjustments"]);
                $empPayrollDetail->charges()->createMany($employeePayrollData["chargings"]);
                if(sizeof($employeePayrollData["deductions"]) > 0) {
                    PayrollDetailDeduction::create($this->setPayrollDetails($employeePayrollData["deductions"], $empPayrollDetail));
                }
            }
            $payroll->refresh();
            if ($payroll->getNextPendingApproval()) {
                Users::find($payroll->getNextPendingApproval()['user_id'])->notify(new PayrollRequestForApproval($payroll));
            }

        });
        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully saved.',
        ], JsonResponse::HTTP_OK);
        // } catch (Exception $e) {
        //     Log::error($e);
        // }
        return new JsonResponse([
            'success' => false,
            'error' => $e,
            'message' => 'Failed to save payroll request.',
        ], 500);

    }

    public function setPayrollDetails($deductions, $empPayrollDetail)
    {
        foreach ($deductions as $data) {
            $paymentStore = [
                "posting_status" => PostingStatusType::NOTPOSTED->value,
                "payment_type" => LoanPaymentsType::PAYROLL->value,
                "date_paid" => Carbon::now()->format('Y-m-d'),
                "amount_paid" => $data["amount"],
            ];
            switch ($data["type"]) {
                case PayrollDetailsDeductionType::CASHADVANCE->value:
                    $paymentStore["cashadvance_id"] = $data["deduction_id"];
                    $thisPayment = CashAdvancePayments::create($paymentStore);
                    return $this->preparePayrollDetailDeduction($data, $thisPayment, $empPayrollDetail);
                    break;
                case PayrollDetailsDeductionType::LOAN->value:
                    $paymentStore["loans_id"] = $data["deduction_id"];
                    $thisPayment = LoanPayments::create($paymentStore);
                    return $this->preparePayrollDetailDeduction($data, $thisPayment, $empPayrollDetail);
                    break;
                case PayrollDetailsDeductionType::OTHERDEDUCTION->value:
                    $paymentStore["otherdeduction_id"] = $data["deduction_id"];
                    $thisPayment = OtherDeductionPayments::create($paymentStore);
                    return $this->preparePayrollDetailDeduction($data, $thisPayment, $empPayrollDetail);
                    break;
            }
        }
    }

    public function preparePayrollDetailDeduction($data, $thisPayment, $empPayrollDetail)
    {
        $data["payroll_details_id"] = $empPayrollDetail->id;
        $data["deduction_type"] = $this->getChargingModel($data["type"]);
        $data["deduction_id"] = $thisPayment->id;
        // $data["charge_type"] = $this->getChargingModel($data["type"]);
        // $data["charge_id"] = $thisPayment->id;
        return $data;
    }

    public function getChargingModel($type)
    {
        switch ($type) {
            case PayrollDetailsDeductionType::CASHADVANCE->value:
                return PayrollRecordController::CASHADVANCE_PAYMENT;
                break;
            case PayrollDetailsDeductionType::LOAN->value:
                return PayrollRecordController::LOANS_PAYMENT;
                break;
            case PayrollDetailsDeductionType::OTHERDEDUCTION->value:
                return PayrollRecordController::OTHERDEDUCTION_PAYMENT;
                break;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $request = PayrollRecord::find($id);
        if (!is_null($request)) {
            return new JsonResponse([
                'success' => true,
                'message' => 'Payrollrecord request fetched.',
                'data' => new PayrollRequestResource($request),
            ], JsonResponse::HTTP_OK);
        }
        return new JsonResponse([
            'success' => false,
            'message' => 'No data found.',
        ], 404);
    }

    public function index()
    {
        $allRequests = $this->payrollService->getAll();
        if (!is_null($allRequests)) {
            return new JsonResponse([
                'success' => true,
                'message' => 'Payrollrecord request fetched.',
                'data' => PaginateResourceCollection::paginate(collect(PayrollRequestResource::collection($allRequests))),
            ], JsonResponse::HTTP_OK);
        }
        return new JsonResponse([
            'success' => false,
            'message' => 'No data found.',
        ], 404);
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
            'data' => PaginateResourceCollection::paginate(collect(PayrollRequestResource::collection($myRequest))),
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
            'data' => PayrollRequestResource::collection($myApproval)
        ]);
    }
}

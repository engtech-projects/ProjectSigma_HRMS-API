<?php

namespace App\Http\Controllers;

use App\Enums\PayrollDetailsDeductionType;
use App\Enums\PostingStatusType;
use App\Enums\LoanPaymentsType;
use App\Enums\RequestStatuses;
use App\Helpers;
use App\Models\Employee;
use App\Models\PayrollRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Services\EmployeeService;
use App\Http\Requests\GeneratePayrollRequest;
use App\Http\Requests\PayrollRecordsListFilterRequest;
use App\Http\Requests\PayrollRecordsRequest;
use App\Http\Services\Payroll\PayrollService;
use App\Http\Requests\StorePayrollRecordRequest;
use App\Http\Resources\PayrollRequestResource;
use App\Models\Department;
use App\Models\CashAdvancePayments;
use App\Models\LoanPayments;
use App\Models\OtherDeductionPayments;
use App\Models\PayrollDetail;
use App\Models\Project;
use App\Notifications\PayrollRequestForApproval;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PayrollRecordController extends Controller
{
    // 'Cash Advance','Loan','Other Deduction','Others'
    public const CASHADVANCE = \App\Models\CashAdvance::class;
    public const LOANS = \App\Models\Loans::class;
    public const OTHERDEDUCTION = \App\Models\OtherDeduction::class;
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
        $employeeDtr = Employee::whereIn('id', $filters['employee_ids'])->with("current_employment.employee_salarygrade")->orderBy("family_name")->get();
        $employeePayrollGeneratedSamePayrollDate = PayrollDetail::whereIn('employee_id', $filters['employee_ids'])->whereHas("payroll_record", function ($query) use ($filters) {
            $query->where("payroll_date", $filters["payroll_date"])
            ->whereIn("request_status", [RequestStatuses::APPROVED, RequestStatuses::PENDING]);
        })->get()->pluck("employee_id")->all();
        // Employee Employment and Payroll Validity Checking
        foreach ($employeeDtr as $employee) {
            if (!$employee->current_employment) {
                return new JsonResponse([
                    'success' => false,
                    'message' => "Employee ".$employee->fullname_first." is not Employed.",
                ], 400);
            }
            if (!$employee->current_employment->position_id) {
                return new JsonResponse([
                    'success' => false,
                    'message' => "Employee ".$employee->fullname_first." has no Position Set.",
                ], 400);
            }
            if (!$employee->current_employment->employee_salarygrade) {
                return new JsonResponse([
                    'success' => false,
                    'message' => "Employee ".$employee->fullname_first." has no Salary Grade Set.",
                ], 400);
            }
            if (in_array($employee->id, $employeePayrollGeneratedSamePayrollDate)) {
                return new JsonResponse([
                    'success' => false,
                    'message' => "Employee ".$employee->fullname_first." has already Pending/Approved generated payroll for this payroll date.",
                ], 400);
            }
        }
        try {
            $result = collect($employeeDtr)->map(function ($employee) use ($periodDates, $filters) {
                $employee["payroll_records"] = $this->employeeService->generatePayroll($periodDates, $filters, $employee);
                $employee->current_employment['position'] = $employee->current_employment->position;
                $employee->current_position = $employee->current_position_name;
                $employee->current_salarygrade = $employee->current_salarygrade_and_step;
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
        $attribute['request_status'] = RequestStatuses::PENDING->value;
        $attribute['created_by'] = auth()->user()->id;
        $attribute["charging_type"] = $attribute["group_type"];
        DB::transaction(function () use ($attribute) {
            $payroll = PayrollRecord::create($attribute);
            foreach ($attribute["payroll_details"] as $employeePayrollData) {
                $empPayrollDetail = $payroll->payroll_details()->create($employeePayrollData);
                $empPayrollDetail->adjustments()->createMany($employeePayrollData["adjustments"]);
                $empPayrollDetail->charges()->createMany($employeePayrollData["chargings"]);
                if (sizeof($employeePayrollData["deductions"]) > 0) {
                    $empPayrollDetail->deductions()->createMany($this->setPayrollDetails($employeePayrollData["deductions"], $empPayrollDetail));
                }
            }
            $payroll->refresh();
            $payroll->notifyNextApprover(PayrollRequestForApproval::class);
        });
        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully saved.',
        ], JsonResponse::HTTP_OK);
        return new JsonResponse([
            'success' => false,
            'error' => $e,
            'message' => 'Failed to save payroll request.',
        ], 500);
    }

    public function setPayrollDetails($deductions, $empPayrollDetail)
    {
        $createDeductions = [];
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
                    array_push($createDeductions, $this->preparePayrollDetailDeduction($data, $thisPayment, $empPayrollDetail));
                    break;
                case PayrollDetailsDeductionType::LOAN->value:
                    $paymentStore["loans_id"] = $data["deduction_id"];
                    $thisPayment = LoanPayments::create($paymentStore);
                    array_push($createDeductions, $this->preparePayrollDetailDeduction($data, $thisPayment, $empPayrollDetail));
                    break;
                case PayrollDetailsDeductionType::OTHERDEDUCTION->value:
                    $paymentStore["otherdeduction_id"] = $data["deduction_id"];
                    $thisPayment = OtherDeductionPayments::create($paymentStore);
                    array_push($createDeductions, $this->preparePayrollDetailDeduction($data, $thisPayment, $empPayrollDetail));
                    break;
            }
        }
        return $createDeductions;
    }

    public function preparePayrollDetailDeduction($data, $thisPayment, $empPayrollDetail)
    {
        $data["payroll_details_id"] = $empPayrollDetail->id;
        $data["deduction_type"] = $this->getDeductionPaymentChargingModel($data["type"]);
        $data["deduction_id"] = $thisPayment->id;
        // $data["charge_type"] = $this->getDeductionPaymentChargingModel($data["type"]);
        // $data["charge_id"] = $thisPayment->id;
        return $data;
    }

    public function getDeductionPaymentChargingModel($type)
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

    public function index(PayrollRecordsListFilterRequest $request)
    {
        $allRequests = PayrollRecord::when($request->has("payroll_date") && $request->payroll_date != '', function ($query) use ($request) {
            return $query->whereDate("payroll_date", $request->payroll_date);
        })
        ->when($request->has("payroll_type") && $request->payroll_type != '', function ($query) use ($request) {
            $query->where("payroll_type", $request->payroll_type);
        })
        ->when($request->has("release_type") && $request->release_type != '', function ($query) use ($request) {
            $query->where("release_type", $request->release_type);
        })
        ->when($request->has("project_id") && $request->project_id != '', function ($query) use ($request) {
            $query->where("project_id", $request->project_id);
        })
        ->when($request->has("department_id") && $request->department_id != '', function ($query) use ($request) {
            $query->where("department_id", $request->department_id);
        })
        ->orderBy("created_at", "DESC")
        ->paginate(config("app.pagination_per_page", 10));
        return PayrollRequestResource::collection($allRequests)
        ->additional([
            'success' => true,
            'message' => 'Payroll Record Request fetched.',
        ]);
    }

    public function myRequest(PayrollRecordsListFilterRequest $request)
    {
        $myRequest = PayrollRecord::when($request->has("payroll_date") && $request->payroll_date != '', function ($query) use ($request) {
            return $query->whereDate("payroll_date", $request->payroll_date);
        })
        ->when($request->has("payroll_type") && $request->payroll_type != '', function ($query) use ($request) {
            $query->where("payroll_type", $request->payroll_type);
        })
        ->when($request->has("release_type") && $request->release_type != '', function ($query) use ($request) {
            $query->where("release_type", $request->release_type);
        })
        ->when($request->has("project_id") && $request->project_id != '', function ($query) use ($request) {
            $query->where("project_id", $request->project_id);
        })
        ->when($request->has("department_id") && $request->department_id != '', function ($query) use ($request) {
            $query->where("department_id", $request->department_id);
        })
        ->myRequests()
        ->orderBy("created_at", "DESC")
        ->paginate(config("app.pagination_per_page", 10));
        return PayrollRequestResource::collection($myRequest)
        ->additional([
            'success' => true,
            'message' => 'Payroll Record Request fetched.',
        ]);
    }
    /**
     * Show all requests to be approved/reviewed by current user
     */
    public function myApproval(PayrollRecordsListFilterRequest $request)
    {
        $myApproval = PayrollRecord::when($request->has("payroll_date") && $request->payroll_date != '', function ($query) use ($request) {
            return $query->whereDate("payroll_date", $request->payroll_date);
        })
        ->when($request->has("payroll_type") && $request->payroll_type != '', function ($query) use ($request) {
            $query->where("payroll_type", $request->payroll_type);
        })
        ->when($request->has("release_type") && $request->release_type != '', function ($query) use ($request) {
            $query->where("release_type", $request->release_type);
        })
        ->when($request->has("project_id") && $request->project_id != '', function ($query) use ($request) {
            $query->where("project_id", $request->project_id);
        })
        ->when($request->has("department_id") && $request->department_id != '', function ($query) use ($request) {
            $query->where("department_id", $request->department_id);
        })
        ->myApprovals()
        ->orderBy("created_at", "DESC")
        ->paginate(config("app.pagination_per_page", 10));
        return PayrollRequestResource::collection($myApproval)
        ->additional([
            'success' => true,
            'message' => 'Payroll Record Request fetched.',
        ]);
    }
    /**
     *
     * Show all Approved Requests
     */
    public function payrollRecords(PayrollRecordsRequest $request)
    {
        $validatedData = $request->validated();
        $datas = PayrollRecord::isApproved()
        ->whereDate("payroll_date", $validatedData["payroll_date"])
        ->when($request->has("payroll_type") && $validatedData["payroll_type"], function ($query) use ($validatedData) {
            return $query->where("payroll_type", $validatedData["payroll_type"]);
        })
        ->when($request->has("release_type") && $validatedData["release_type"], function ($query) use ($validatedData) {
            return $query->where("release_type", $validatedData["release_type"]);
        })
        ->when($request->has("project_id") && $validatedData["project_id"], function ($query) use ($validatedData) {
            return $query->where("project_id", $validatedData["project_id"]);
        })
        ->when($request->has("department_id") && $validatedData["department_id"], function ($query) use ($validatedData) {
            return $query->where("department_id", $validatedData["department_id"]);
        })
        ->paginate(config("app.pagination_per_page", 10));
        return PayrollRequestResource::collection($datas)
        ->additional([
            'success' => true,
            'message' => 'Payroll Record Request fetched.',
        ]);
    }
}

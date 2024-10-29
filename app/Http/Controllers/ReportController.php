<?php

namespace App\Http\Controllers;

use App\Enums\LoanPaymentPostingStatusType;
use App\Http\Requests\PagibigEmployeeRemittanceRequest;
use App\Http\Requests\PagibigGroupRemittanceRequest;
use App\Http\Requests\PagibigRemittanceSummaryRequest;
use App\Http\Requests\PhilhealthEmployeeRemittanceRequest;
use App\Http\Requests\PhilhealthGroupRemittanceRequest;
use App\Http\Requests\PhilhealthRemittanceSummaryRequest;
use App\Http\Requests\SssEmployeeLoansRequest;
use App\Http\Requests\SssEmployeeRemittanceRequest;
use App\Http\Requests\SssGroupRemittanceRequest;
use App\Http\Requests\sssRemittanceSummaryRequest;
use App\Http\Resources\PagibigEmployeeRemittanceResource;
use App\Http\Resources\PagibigGroupRemittanceResource;
use App\Http\Resources\PagibigRemittanceSummaryResource;
use App\Http\Resources\PhilhealthEmployeeRemittanceResource;
use App\Http\Resources\PhilhealthGroupRemittanceResource;
use App\Http\Resources\philhealthRemittanceSummaryResource;
use App\Http\Resources\SssEmployeeLoanResource;
use App\Http\Resources\SSSEmployeeRemittanceResource;
use App\Http\Resources\SssGroupRemittanceResource;
use App\Http\Resources\sssRemittanceSummaryResource;
use App\Models\LoanPayments;
use App\Models\PayrollDetail;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{

    /**
     * Generates a summary of SSS remittances for a specific employee within a specified date range.
     *
     * This function processes payroll details to filter employee records that have SSS
     * contributions within the given cutoff dates. It groups the results by employee ID and
     * returns a JSON response containing the employee remittance summary data.
     *
     * @param SssEmployeeRemittanceRequest $request The request instance containing validated data,
     * including cutoff_start and cutoff_end for filtering payroll records.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response with success status, message,
     * and a collection of employee SSS remittance summary data.
     */
    public function sssEmployeeRemittanceGenerate(SssEmployeeRemittanceRequest $request)
    {
        $validatedData = $request->validated();
        $data = PayrollDetail::with(["employee.company_employments"])
        ->whereHas('payroll_record', function ($query) use ($validatedData) {
            return $query->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                ->isApproved();
        })
        ->where(function ($query) {
            $query->Where("sss_employee_contribution", ">", 0)
            ->orWhere("sss_employer_contribution", ">", 0)
            ->orWhere("sss_employee_compensation", ">", 0)
            ->orWhere("sss_employer_compensation", ">", 0)
            ->orWhere("sss_employee_wisp", ">", 0)
            ->orWhere("sss_employer_wisp", ">", 0);
        })
        ->orderBy("created_at", "DESC")
        ->get()
        ->append([
            "total_sss_contribution",
            "total_sss_compensation",
            "total_sss_wisp",
            "total_sss",
        ])
        ->sortBy('employee.fullname_last', SORT_NATURAL)
        ->groupBy("employee_id")
        ->map(function ($employeeData) {
            return [
                ...$employeeData->first()->toArray(),
                'sss_employer_contribution' => $employeeData->sum("sss_employer_contribution"),
                'sss_employee_contribution' => $employeeData->sum("sss_employee_contribution"),
                'sss_employer_compensation' => $employeeData->sum("sss_employer_compensation"),
                'sss_employee_compensation' => $employeeData->sum("sss_employee_compensation"),
                'sss_employer_wisp' => $employeeData->sum("sss_employer_wisp"),
                'sss_employee_wisp' => $employeeData->sum("sss_employee_wisp"),
                'total_sss_contribution' => $employeeData->sum("total_sss_contribution"),
                'total_sss_compensation' => $employeeData->sum("total_sss_compensation"),
                'total_sss_wisp' => $employeeData->sum("total_sss_wisp"),
                'total_sss' => $employeeData->sum("total_sss"),
            ];
        })
        ->values()
        ->all();
        return new JsonResponse([
            'success' => true,
            'message' => 'Employee Remittance Request fetched.',
            'data' => SSSEmployeeRemittanceResource::collection($data),
        ]);
    }
    /**
     * Generates a summary of SSS remittances for all projects within a specified date range.
     *
     * This function processes payroll details to filter employee records that have SSS
     * contributions within the given cutoff dates. It groups the results by charging name and
     * returns a JSON response containing the remittance summary data.
     *
     * @param sssGroupRemittanceRequest $request The request instance containing validated data,
     * including cutoff_start and cutoff_end for filtering payroll records.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response with success status, message,
     * and a collection of grouped SSS remittance summary data.
     */
    public function sssGroupRemittanceGenerate(SssGroupRemittanceRequest $request)
    {
        $validatedData = $request->validated();
        $data = PayrollDetail::with(['payroll_record', "employee.company_employments"])
        ->whereHas('payroll_record', function ($query) use ($validatedData) {
            return $query->when(!empty($validatedData['project_id']), function ($query2) use ($validatedData) {
                return $query2->where('project_id', $validatedData["project_id"]);
            })
                ->when(!empty($validatedData['department_id']), function ($query2) use ($validatedData) {
                    return $query2->where('department_id', $validatedData["department_id"]);
                })
                ->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                ->isApproved();
        })
        ->where(function ($query) {
            $query->Where("sss_employee_contribution", ">", 0)
            ->orWhere("sss_employer_contribution", ">", 0)
            ->orWhere("sss_employee_compensation", ">", 0)
            ->orWhere("sss_employer_compensation", ">", 0)
            ->orWhere("sss_employee_wisp", ">", 0)
            ->orWhere("sss_employer_wisp", ">", 0);
        })
        ->orderBy("created_at", "DESC")
        ->get()
        ->append([
            "total_sss_contribution",
            "total_sss_compensation",
            "total_sss_wisp",
            "total_sss",
        ])
        ->sortBy('employee.fullname_last', SORT_NATURAL)
        ->groupBy("employee_id")
        ->map(function ($employeeData) {
            return [
                ...$employeeData->first()->toArray(),
                'sss_employer_contribution' => $employeeData->sum("sss_employer_contribution"),
                'sss_employee_contribution' => $employeeData->sum("sss_employee_contribution"),
                'sss_employer_compensation' => $employeeData->sum("sss_employer_compensation"),
                'sss_employee_compensation' => $employeeData->sum("sss_employee_compensation"),
                'sss_employer_wisp' => $employeeData->sum("sss_employer_wisp"),
                'sss_employee_wisp' => $employeeData->sum("sss_employee_wisp"),
                'total_sss_contribution' => $employeeData->sum("total_sss_contribution"),
                'total_sss_compensation' => $employeeData->sum("total_sss_compensation"),
                'total_sss_wisp' => $employeeData->sum("total_sss_wisp"),
                'total_sss' => $employeeData->sum("total_sss"),
                "payroll_record" => [
                    ...$employeeData->first()->payroll_record->toArray(),
                    "charging_name" => $employeeData->first()->payroll_record->charging_name,
                ],
            ];
        })
        ->values()
        ->all();
        $data = collect($data);
        $firstRecord = $data->first();
        $dataArray = $data->all();
        return new JsonResponse([
            'success' => true,
            'message' => 'Project Remittance Request fetched.',
            'data' => [
                'charging' => $firstRecord['payroll_record']['charging_name'] ?? "",
                'remittances' => SssGroupRemittanceResource::collection($dataArray)
            ],
        ]);
    }
    /**
     * Generates a summary of SSS remittances for all projects within a specified date range.
     *
     * This function processes payroll details to filter employee records that have SSS
     * contributions within the given cutoff dates. It groups the results by charging name and
     * returns a JSON response containing the remittance summary data.
     *
     * @param sssRemittanceSummaryRequest $request The request instance containing validated data,
     * including cutoff_start and cutoff_end for filtering payroll records.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response with success status, message,
     * and a collection of grouped SSS remittance summary data.
     */
    public function sssRemittanceSummary(sssRemittanceSummaryRequest $request)
    {
        $validatedData = $request->validated();
        $data = PayrollDetail::with(['payroll_record', "employee.company_employments"])
        ->whereHas('payroll_record', function ($query) use ($validatedData) {
            return $query
                ->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                ->isApproved();
        })
        ->where(function ($query) {
            $query->Where("sss_employee_contribution", ">", 0)
            ->orWhere("sss_employer_contribution", ">", 0)
            ->orWhere("sss_employee_compensation", ">", 0)
            ->orWhere("sss_employer_compensation", ">", 0)
            ->orWhere("sss_employee_wisp", ">", 0)
            ->orWhere("sss_employer_wisp", ">", 0);
        })
        ->orderBy("created_at", "DESC")
        ->get()
        ->append([
            "total_sss_contribution",
            "total_sss_compensation",
            "total_sss_wisp",
            "total_sss",
        ])
        ->sortBy('payroll_record.charging_name', SORT_NATURAL)
        ->values()
        ->all();
        $uniqueGroup =  collect($data)->groupBy('payroll_record.charging_name');
        return new JsonResponse([
            'success' => true,
            'message' => 'Project Remittance Request fetched.',
            'data' => sssRemittanceSummaryResource::collection($uniqueGroup),
        ]);
    }

    /**
     * Generates a summary of Pagibig remittances for employees within a specified date range.
     *
     * This function processes payroll details to filter employee records that have Pagibig
     * contributions within the given cutoff dates. It groups the results by employee and
     * returns a JSON response containing the remittance data.
     *
     * @param PagibigEmployeeRemittanceRequest $request The request instance containing validated data,
     * including cutoff_start and cutoff_end for filtering payroll records.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response with success status, message,
     * and a collection of grouped Pagibig employee remittance summary data.
     */
    public function pagibigEmployeeRemittanceGenerate(PagibigEmployeeRemittanceRequest $request)
    {
        $validatedData = $request->validated();
        $data = PayrollDetail::with(["employee.company_employments"])
        ->whereHas('payroll_record', function ($query) use ($validatedData) {
            return $query->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                ->isApproved();
        })
        ->where(function ($query) {
            $query->where("pagibig_employee_contribution", ">", 0)
            ->orWhere("pagibig_employer_contribution", ">", 0);
        })
        ->orderBy("created_at", "DESC")
        ->get()
        ->append([
            "total_pagibig_contribution",
        ])
        ->sortBy('employee.fullname_last', SORT_NATURAL)
        ->groupBy("employee_id")
        ->map(function ($employeeData) {
            return [
                ...$employeeData->first()->toArray(),
                'pagibig_employee_contribution' => $employeeData->sum("pagibig_employee_contribution"),
                'pagibig_employer_contribution' => $employeeData->sum("pagibig_employer_contribution"),
                'total_pagibig_contribution' => $employeeData->sum("total_pagibig_contribution"),
            ];
        })
        ->values()
        ->all();
        return new JsonResponse([
            'success' => true,
            'message' => 'Employee Remittance Request fetched.',
            'data' => PagibigEmployeeRemittanceResource::collection($data),
        ]);
    }
    /**
     * Generates a summary of Pagibig remittances for employees within a specified date range.
     *
     * This function processes payroll details to filter employee records that have Pagibig
     * contributions within the given cutoff dates. It groups the results by charging name and
     * returns a JSON response containing the remittance data.
     *
     * @param PagibigGroupRemittanceRequest $request The request instance containing validated data,
     * including cutoff_start and cutoff_end for filtering payroll records.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response with success status, message,
     * and a collection of grouped Pagibig remittance summary data.
     */
    public function pagibigGroupRemittanceGenerate(PagibigGroupRemittanceRequest $request)
    {
        $validatedData = $request->validated();
        $data = PayrollDetail::with(['payroll_record', "employee.company_employments"])
        ->whereHas('payroll_record', function ($query) use ($validatedData) {
            return $query->when(!empty($validatedData['project_id']), function ($query2) use ($validatedData) {
                return $query2->where('project_id', $validatedData["project_id"]);
            })
                ->when(!empty($validatedData['department_id']), function ($query2) use ($validatedData) {
                    return $query2->where('department_id', $validatedData["department_id"]);
                })
                ->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                ->isApproved();
        })
        ->where(function ($query) {
            $query->where("pagibig_employee_contribution", ">", 0)
            ->orWhere("pagibig_employer_contribution", ">", 0);
        })
        ->orderBy("created_at", "DESC")
        ->get()
        ->append([
            "total_pagibig_contribution",
        ])
        ->sortBy('employee.fullname_last', SORT_NATURAL)
        ->groupBy("employee_id")
        ->map(function ($employeeData) {
            return [
                ...$employeeData->first()->toArray(),
                'pagibig_employee_contribution' => $employeeData->sum("pagibig_employee_contribution"),
                'pagibig_employer_contribution' => $employeeData->sum("pagibig_employer_contribution"),
                'total_pagibig_contribution' => $employeeData->sum("total_pagibig_contribution"),
                "payroll_record" => [
                    ...$employeeData->first()->payroll_record->toArray(),
                    "charging_name" => $employeeData->first()->payroll_record->charging_name,
                ],
            ];
        })
        ->values()
        ->all();
        $data = collect($data);
        $firstRecord = $data->first();
        $dataArray = $data->all();
        return new JsonResponse([
            'success' => true,
            'message' => 'Project Remittance Request fetched.',
            'data' => [
                'charging' => $firstRecord['payroll_record']['charging_name'] ?? "",
                'remittances' => PagibigGroupRemittanceResource::collection($dataArray)
            ],
        ]);
    }
    /**
     * Generates a summary of Pagibig remittances for employees within a specified date range.
     *
     * This function processes payroll details to filter employee records that have Pagibig
     * contributions within the given cutoff dates. It groups the results by charging name and
     * returns a JSON response containing the remittance data.
     *
     * @param PagibigRemittanceSummaryRequest $request The request instance containing validated data,
     * including cutoff_start and cutoff_end for filtering payroll records.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response with success status, message,
     * and a collection of grouped Pagibig remittance summary data.
     */
    public function pagibigRemittanceSummary(PagibigRemittanceSummaryRequest $request)
    {
        $validatedData = $request->validated();
        $data = PayrollDetail::with(['payroll_record', "employee.company_employments"])
        ->whereHas('payroll_record', function ($query) use ($validatedData) {
            return $query
                ->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                ->isApproved();
        })
        ->where(function ($query) {
            $query->where("pagibig_employee_contribution", ">", 0)
            ->orWhere("pagibig_employer_contribution", ">", 0);
        })
        ->orderBy("created_at", "DESC")
        ->get()
        ->append([
            "total_pagibig_contribution",
        ])
        ->sortBy('payroll_record.charging_name', SORT_NATURAL)
        ->values()
        ->all();
        $uniqueGroup =  collect($data)->groupBy('payroll_record.charging_name');
        return new JsonResponse([
            'success' => true,
            'message' => 'Project Remittance Request fetched.',
            'data' => PagibigRemittanceSummaryResource::collection($uniqueGroup),
        ]);
    }

    /**
     * Philhealth Employee Remittance Generate
     *
     * @param PhilhealthEmployeeRemittanceRequest $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function philhealthEmployeeRemittanceGenerate(PhilhealthEmployeeRemittanceRequest $request)
    {
        $validatedData = $request->validated();
        $data = PayrollDetail::with(["employee.company_employments"])
        ->whereHas('payroll_record', function ($query) use ($validatedData) {
            return $query->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                ->isApproved();
        })
        ->where(function ($query) {
            $query->where("philhealth_employee_contribution", ">", 0)
            ->orWhere("philhealth_employer_contribution", ">", 0);
        })
        ->get()
        ->append([
            "total_philhealth_contribution"
        ])
        ->sortBy('employee.fullname_last', SORT_NATURAL)
        ->groupBy("employee_id")
        ->map(function ($employeeData) {
            return [
                ...$employeeData->first()->toArray(),
                'philhealth_employee_contribution' => $employeeData->sum("philhealth_employee_contribution"),
                'philhealth_employer_contribution' => $employeeData->sum("philhealth_employer_contribution"),
                'total_philhealth_contribution' => $employeeData->sum("total_philhealth_contribution"),
            ];
        })
        ->values()
        ->all();
        return new JsonResponse([
            'success' => true,
            'message' => 'Employee Remittance Request fetched.',
            'data' => PhilhealthEmployeeRemittanceResource::collection($data),
        ]);
    }

    /**
     * Generates a summary of Philhealth remittances for employees within a specified date range.
     *
     * This function processes payroll details to filter employee records that have Philhealth
     * contributions within the given cutoff dates. It groups the results by charging name and
     * returns a JSON response containing the remittance data.
     *
     * @param PhilhealthGroupRemittanceRequest $request The request instance containing validated data,
     * including cutoff_start and cutoff_end for filtering payroll records.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response with success status, message,
     * and a collection of grouped Philhealth remittance summary data.
     */
    public function philhealthGroupRemittanceGenerate(PhilhealthGroupRemittanceRequest $request)
    {
        $validatedData = $request->validated();
        $data = PayrollDetail::with(['payroll_record', "employee.company_employments"])
        ->whereHas('payroll_record', function ($query) use ($validatedData) {
            return $query->when(!empty($validatedData['project_id']), function ($query2) use ($validatedData) {
                return $query2->where('project_id', $validatedData["project_id"]);
            })
                ->when(!empty($validatedData['department_id']), function ($query2) use ($validatedData) {
                    return $query2->where('department_id', $validatedData["department_id"]);
                })
                ->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                ->isApproved();
        })
        ->where(function ($query) {
            $query->where("philhealth_employee_contribution", ">", 0)
            ->orWhere("philhealth_employer_contribution", ">", 0);
        })
        ->orderBy("created_at", "DESC")
        ->get()
        ->append([
            "total_philhealth_contribution"
        ])
        ->sortBy('employee.fullname_last', SORT_NATURAL)
        ->groupBy("employee_id")
        ->map(function ($employeeData) {
            return [
                ...$employeeData->first()->toArray(),
                'philhealth_employee_contribution' => $employeeData->sum("philhealth_employee_contribution"),
                'philhealth_employer_contribution' => $employeeData->sum("philhealth_employer_contribution"),
                'total_philhealth_contribution' => $employeeData->sum("total_philhealth_contribution"),
                "payroll_record" => [
                    ...$employeeData->first()->payroll_record->toArray(),
                    "charging_name" => $employeeData->first()->payroll_record->charging_name,
                ],
            ];
        })
        ->values()
        ->all();
        $data = collect($data);
        $firstRecord = $data->first();
        $dataArray = $data->all();
        return new JsonResponse([
            'success' => true,
            'message' => 'Project Remittance Request fetched.',
            'data' => [
                'charging' => $firstRecord['payroll_record']['charging_name'] ?? "",
                'remittances' => PhilhealthGroupRemittanceResource::collection($dataArray)
            ],
        ]);
    }

    /**
     * Generates a summary of Philhealth remittances for employees within a specified date range.
     *
     * This function processes payroll details to filter employee records that have Philhealth
     * contributions within the given cutoff dates. It groups the results by charging name and
     * returns a JSON response containing the remittance data.
     *
     * @param PhilhealthRemittanceSummaryRequest $request The request instance containing validated data,
     * including cutoff_start and cutoff_end for filtering payroll records.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response with success status, message,
     * and a collection of grouped Philhealth remittance summary data.
     */
    public function philhealthRemittanceSummary(PhilhealthRemittanceSummaryRequest $request)
    {
        $validatedData = $request->validated();
        $data = PayrollDetail::with(['payroll_record', "employee.company_employments"])
        ->whereHas('payroll_record', function ($query) use ($validatedData) {
            return $query
                ->whereBetween('payroll_date', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
                ->isApproved();
        })
        ->where(function ($query) {
            $query->where("philhealth_employee_contribution", ">", 0)
            ->orWhere("philhealth_employer_contribution", ">", 0);
        })
        ->orderBy("created_at", "DESC")
        ->get()
        ->append([
            "total_philhealth_contribution"
        ])
        ->sortBy('payroll_record.charging_name', SORT_NATURAL)
        ->values()
        ->all();
        $uniqueGroup =  collect($data)->groupBy('payroll_record.charging_name');
        return new JsonResponse([
            'success' => true,
            'message' => 'Project Remittance Request fetched.',
            'data' => philhealthRemittanceSummaryResource::collection($uniqueGroup),
        ]);
    }
    /**
     * Generates a summary of SSS loans for employees within a specified date range.
     *
     * This function processes loan payments to filter employee records that have SSS
     * loan payments within the given cutoff dates. It groups the results by employee ID and
     * returns a JSON response containing the summary data.
     *
     * @param SssEmployeeLoansRequest $request The request instance containing validated data,
     * including cutoff_start and cutoff_end for filtering loan payments.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response with success status, message,
     * and a collection of employee SSS loan summary data.
     */
    public function sssEmployeeLoans(SssEmployeeLoansRequest $request)
    {
        $validatedData = $request->validated();
        $data = LoanPayments::whereHas('loan', function ($query) use ($validatedData) {
            return $query->where('posting_status', LoanPaymentPostingStatusType::POSTED->value)
                ->when(!empty($validatedData['loan_type']), function ($query2) use ($validatedData) {
                    return $query2->where('name', $validatedData["loan_type"]);
                });
        })
        ->with(['loan.employee.company_employments'])
        ->whereBetween('date_paid', [$validatedData['cutoff_start'], $validatedData['cutoff_end']])
        ->orderBy("created_at", "DESC")
        ->get()
        ->groupBy('loan.employee.id')
        ->map(function ($employeeData) {
            return [
                'employee_name' => $employeeData->first()->loan->employee->fullname_last,
                'total_amount_payment' => $employeeData->sum('amount_paid'),
                'sss_number' => $employeeData->first()->loan->employee->company_employments->sss_number,
            ];
        });
        return new JsonResponse([
            'success' => true,
            'message' => 'Project Remittance Request fetched.',
            'data' => SssEmployeeLoanResource::collection($data),
        ]);
    }
}

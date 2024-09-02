<?php

namespace App\Http\Controllers;

use App\Enums\LoanPaymentsType;
use App\Http\Requests\LoanPaymentRequest;
use App\Http\Requests\LoansAllRequest;
use App\Models\Loans;
use App\Http\Requests\StoreLoansRequest;
use App\Http\Requests\UpdateLoansRequest;
use App\Utils\PaginateResourceCollection;
use Illuminate\Http\JsonResponse;

class LoansController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(LoansAllRequest $request)
    {
        $validatedData = $request->validated();
        $data = Loans::when($request->has('employee_id'), function($query) use ($validatedData) {
            return $query->whereHas('employee', function($query2) use ($validatedData) {
                $query2->where('employee_id', $validatedData["employee_id"]);
            });
        })
        ->with(['employee', 'loan_payments_employee'])
        ->orderBy("created_at", "DESC")
        ->paginate(15);

        return new JsonResponse([
            'success' => true,
            'message' => 'Loan Request fetched.',
            'data' => $data
        ]);
    }

    public function ongoing(LoansAllRequest $request)
    {
        $validatedData = $request->validated();
        $data = Loans::when($request->has('employee_id'), function($query) use ($validatedData) {
            return $query->whereHas('employee', function($query2) use ($validatedData) {
                $query2->where('employee_id', $validatedData["employee_id"]);
            });
        })
        ->with(['employee', 'loan_payments_employee'])
        ->orderBy("created_at", "DESC")
        ->get();
        $data = collect($data->filter(function ($loan) {
            return !$loan->loanPaid();
        })
        ->values()
        ->all());
        return new JsonResponse([
            'success' => true,
            'message' => 'Loan Request fetched.',
            'data' => PaginateResourceCollection::paginate($data)
        ]);
    }

    public function paid(LoansAllRequest $request)
    {
        $validatedData = $request->validated();
        $data = Loans::when($request->has('employee_id'), function($query) use ($validatedData) {
            return $query->whereHas('employee', function($query2) use ($validatedData) {
                $query2->where('employee_id', $validatedData["employee_id"]);
            });
        })
        ->with(['employee', 'loan_payments_employee'])
        ->orderBy("created_at", "DESC")
        ->get();
        $data = collect($data->filter(function ($loan) {
            return $loan->loanPaid();
        })
        ->values()
        ->all());
        return new JsonResponse([
            'success' => true,
            'message' => 'Loan Request fetched.',
            'data' => PaginateResourceCollection::paginate($data)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLoansRequest $request)
    {
        $main = new Loans();
        $main->fill($request->validated());
        $data = json_decode('{}');

        if (!$main->save()) {
            $data->message = "Save failed.";
            $data->success = false;
            return response()->json($data, 400);
        }

        $data->message = "Successfully save.";
        $data->success = true;
        $data->data = $main;
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $main = Loans::find($id);
        $data = json_decode('{}');

        if (!is_null($main)) {
            $data->message = "Successfully fetch.";
            $data->success = true;
            $data->data = $main;
            return response()->json($data);
        }

        $data->message = "No data found.";
        $data->success = false;
        return response()->json($data, 404);
    }

    public function loanPayment(Loans $loan, LoanPaymentRequest $request)
    {
        $valid = true;
        $msg = "";
        $validatedData = $request->validated();
        if ($loan->loanPaid()) {
            $valid = false;
            $msg = "Payment already paid.";
        } elseif ($loan->paymentWillOverpay($validatedData['paymentAmount'])) {
            $valid = false;
            $msg = "Payment will overpay.";
        } else {
            $loan->loanPayment($validatedData['paymentAmount'], LoanPaymentsType::MANUAL->value);
            $valid = true;
            $msg = "Payment successfully.";
        }

        $loan->refresh();

        return new JsonResponse([
            'success' => $valid,
            'message' => $msg,
            "data" => $loan
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLoansRequest $request, $id)
    {
        $main = Loans::find($id);
        $data = json_decode('{}');

        if (!is_null($main)) {
            $main->fill($request->validated());
            if ($main->save()) {
                $data->message = "Successfully update.";
                $data->success = true;
                $data->data = $main;
                return response()->json($data);
            }
            $data->message = "Update failed.";
            $data->success = false;
            return response()->json($data, 400);
        }

        $data->message = "Failed update.";
        $data->success = false;
        return response()->json($data, 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $main = Loans::find($id);
        $data = json_decode('{}');

        if (!is_null($main)) {
            if ($main->delete()) {
                $data->message = "Successfully delete.";
                $data->success = true;
                $data->data = $main;
                return response()->json($data);
            }
            $data->message = "Failed delete.";
            $data->success = false;
            return response()->json($data, 400);
        }

        $data->message = "Failed delete.";
        $data->success = false;
        return response()->json($data, 404);
    }
}

<?php

namespace App\Http\Controllers;

use App\Enums\LoanPaymentsType;
use App\Http\Requests\LoanPaymentRequest;
use App\Models\Loans;
use App\Http\Requests\StoreLoansRequest;
use App\Http\Requests\UpdateLoansRequest;
use Illuminate\Http\JsonResponse;

class LoansController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $main = Loans::with("employee", "loan_payments_employee")->paginate(15);
        $data = json_decode('{}');
        $data->message = "Successfully fetch.";
        $data->success = true;
        $data->data = $main;
        return response()->json($data);
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

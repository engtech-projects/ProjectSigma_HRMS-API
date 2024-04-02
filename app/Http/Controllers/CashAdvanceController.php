<?php

namespace App\Http\Controllers;

use App\Enums\LoanPaymentsType;
use App\Http\Requests\cashAdvanceRequest;
use App\Models\CashAdvance;
use App\Http\Requests\StoreCashAdvanceRequest;
use App\Http\Requests\UpdateCashAdvanceRequest;
use Illuminate\Http\JsonResponse;

class CashAdvanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $main = CashAdvance::with("employee", "department", "project")->paginate(15);
        $data = json_decode('{}');
        $data->message = "Successfully fetch.";
        $data->success = true;
        $data->data = $main;
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCashAdvanceRequest $request)
    {
        $main = new CashAdvance;
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

    function cashAdvancePayment(CashAdvance $cash, CashAdvanceRequest $request)
    {
        $valid = true;
        $msg = "";

        if ($cash->cashPaid()) {
            $valid = false;
            $msg = "Payment already paid.";
        } elseif ($cash->paymentWillOverpay($request->paymentAmount)) {
            $valid = false;
            $msg = "Payment will overpay.";
        } else {
            $cash->cashAdvance($request->paymentAmount, LoanPaymentsType::MANUAL->value);
            $valid = true;
            $msg = "Payment successfully.";
        }

        $cash->refresh();

        return new JsonResponse([
            'success' => $valid,
            'message' => $msg,
            "data" => $cash
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $main = CashAdvance::with("employee", "department", "project")->find($id);
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

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCashAdvanceRequest $request,  $id)
    {
        $main = CashAdvance::find($id);
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
        $main = CashAdvance::find($id);
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
